<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Entity\Office;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianCorrectionLog;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Entity\WorkflowTeam;
use PorchaProcessingBundle\Generator\IdGenerator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

class WorkflowManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;
    protected $offices;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        if($this->user != 'anon.') {
            $this->office = $this->user->getOffice();

            $this->offices = array($this->office->getId());
            if ($this->user->getOffice()->getChildren()) {
                foreach ($this->user->getOffice()->getChildren() as $children) {
                    $this->offices[] = $children->getId();
                }
            }
        }

    }

    public function workflowAction(Khatian $khatian, $actionName, $correctionLogs, $currentUser) {

        $khatianStatus = '';
        $khatianVersion = $khatian->getLastVersion();
        $correctionCycle = ($khatian->getCorrectionCycle()) ? $khatian->getCorrectionCycle() : 0;

        if (in_array('ROLE_KHATIAN_VERIFICATION', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'READY_FOR_COMPARISON';
            } else {
                $khatian->setCorrectionCycle($correctionCycle + 1);
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianVersion->setVerifier($currentUser);
            $khatianVersion->setVerifiedAt(new \DateTime());
            
        } else if (in_array('ROLE_KHATIAN_COMPARISON', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'READY_FOR_APPROVAL';
            } else {
                $khatian->setCorrectionCycle($correctionCycle + 1);
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianVersion->setComparer($currentUser);
            $khatianVersion->setComparedAt(new \DateTime());

        } else if (in_array('ROLE_KHATIAN_APPROVAL', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'APPROVED';
                $khatian->setArchived(true);
                //$this->setReadyForDelivery($khatian);
            } else {
                $khatian->setCorrectionCycle($correctionCycle + 1);
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianVersion->setApprover($currentUser);
            $khatianVersion->setApprovedAt(new \DateTime());
        }

        if ($khatianStatus !== 'CORRECTION_REQUIRED') {
            $this->resetCorrectionMessage($khatianVersion);
        } else {
            /** @var KhatianCorrectionLog $correctionLog */
            foreach ($correctionLogs as $correctionLog) {
                $correctionLog->setUser($currentUser);
            }
        }

        $khatian->setStatus($khatianStatus);
        $khatian->setLockedBy(null)->setLockedAt(null);

        $this->em->persist($khatian);
        $this->em->persist($khatianVersion);
        $this->em->flush();
    }

    public function srWorkflowAction(KhatianLog $khatianLog, $actionName, $correctionLogs, $currentUser) {

        $khatianStatus = '';
        /** @var  $khatian */
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();
        $khatianVersion = $khatianLog->getKhatianVersion();

        if (in_array('ROLE_KHATIAN_ENTRY', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'READY_FOR_VERIFICATION';
                //$this->changeServiceRequestStatus($khatianLog, 'PROCESSING');
            } else {
                $khatianStatus = 'DRAFT';
            }
            $khatianLog->setEntryOperator($currentUser);
            $khatianLog->setEntryAt(new \DateTime());

        } else if (in_array('ROLE_KHATIAN_VERIFICATION', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'READY_FOR_COMPARISON';
            } else {
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianLog->setVerifier($currentUser);
            $khatianLog->setVerifiedAt(new \DateTime());

        } else if (in_array('ROLE_KHATIAN_COMPARISON', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'READY_FOR_APPROVAL';
            } else {
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianLog->setComparer($currentUser);
            $khatianLog->setComparedAt(new \DateTime());

        } else if (in_array('ROLE_KHATIAN_APPROVAL', $currentUser->getRoles())) {

            if ($actionName == 'ACTION_FORWARD') {
                $khatianStatus = 'APPROVED';
                $khatian->setArchived(true);
                $khatian->setArchivedAt(new \DateTime());
                $khatian->setReCorrection(false);
//                if (!$khatianLog->isBatch()) {
//                    $this->changeServiceRequestStatus($khatianLog, 'READY_FOR_DELIVERY');
//                }

            } else {
                $khatianStatus = 'CORRECTION_REQUIRED';
            }
            $khatianLog->setApprover($currentUser);
            $khatianLog->setApprovedAt(new \DateTime());
        }

        if ($khatianStatus !== 'CORRECTION_REQUIRED') {
            $this->resetCorrectionMessage($khatianVersion);
        } else {

            if (!$khatianLog->isBatch()) {
                /** @var KhatianVersion $khatianVersion */
                $khatianVersion = $khatianLog->getKhatianVersion();
                if ($khatianVersion->isNewVersionRequired()) {

                    $khatianVersion->setNewVersionRequired(false);
                    $newKhatianVersion = clone $khatianVersion;
                    $newKhatianVersion->setId(null);
                    $newKhatianVersion->setNewVersionRequired(false);
                    $newKhatianVersion->setIdentifier(IdGenerator::getID());
                    $this->em->persist($khatianVersion);
                    $this->em->persist($newKhatianVersion);

                    $pages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $khatianVersion));
                    foreach ($pages as $page) {
                        $newPage = clone $page;
                        $newPage->setId(null);
                        $newPage->setKhatianVersion($newKhatianVersion);
                        $this->em->persist($newPage);

                        if ($page->getCorrectionLog()) {
                            $cLog = $page->getCorrectionLog();
                            $cLog->setKhatianPage($newPage);
                            $cLog->setUser($currentUser);
                            $this->em->persist($cLog);
                        }
                    }
                    /** @var Khatian $khatian */
                    $khatian->setLastVersion($newKhatianVersion);
                    $this->em->persist($khatian);

                    $khatianLogs = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->findBy(array(
                        'khatianVersion' => $khatianVersion
                    ));

                    if ($khatianLogs) {
                        foreach ($khatianLogs as $kl) {
                            $kl->setKhatianVersion($newKhatianVersion);
                            $this->em->persist($kl);
                        }
                    }
                }
            } else {
                if ($correctionLogs) {
                    /** @var KhatianCorrectionLog $correctionLog */
                    foreach ($correctionLogs as $correctionLog) {
                        $correctionLog->setUser($currentUser);
                    }
                }
            }
        }

        $khatianLog->setKhatianStatus($khatianStatus);
        $this->em->persist($khatianLog);
        $this->em->persist($khatian);
        $this->em->persist($khatianVersion);

        $this->em->flush();
    }

    public function isFirstApp(KhatianLog $khatianLog) {
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();
        $row = $this->em->getRepository('PorchaProcessingBundle:Khatian')->findOneBy(array('id' => $khatian->getId(), 'archived' => '1'));
        return ($row) ? true : false;
    }

    public function changeServiceRequestStatus(KhatianLog $khatianLog, $status) {

        $serviceRequestPorcha = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findOneBy(array('khatianLog' => $khatianLog));
        $serviceRequest = $serviceRequestPorcha->getServiceRequest();
        $serviceRequest->setStatus($status);
        $this->em->persist($serviceRequest);
        $this->em->flush();
    }

    public function getDraftKhatians($data, $count = false) {

        $qb = $this->queryKhatians();
        $qb->andWhere('kl.entryOperator = :entryOperator');
        $qb->setParameter('entryOperator', $this->user);
        $qb->andWhere("kl.khatianStatus = 'DRAFT' ");

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        $this->queryKhatiansOrderBy($qb, $this->queryParameters($data), 'DRAFT');

        return $qb->getQuery()->getResult();
    }

    public function getSRDraftKhatians($data, $count = false) {

        $qb = $this->serviceRequestQueryKhatians();
        $qb->andWhere('kl.entryOperator = :entryOperator');
        $qb->setParameter('entryOperator', $this->user);
        $qb->andWhere("kl.khatianStatus = 'DRAFT' ");

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(p.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function getSentKhatianList($data, $count = false) {

        $qb = $this->queryKhatians();
        $qb->andWhere('kl.entryOperator = :entryOperator');
        $qb->setParameter('entryOperator', $this->user);
        $qb->andWhere("kl.khatianStatus IN ('READY_FOR_VERIFICATION', 'READY_FOR_COMPARISON', 'READY_FOR_APPROVAL') ");

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        $this->queryKhatiansOrderBy($qb, $this->queryParameters($data), 'SENT_KHATIANS');

        return $qb->getQuery()->getResult();
    }

    public function getSRSentKhatianList($data, $count = false) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->innerJoin('p.serviceRequest', 'sr');
        $qb->innerJoin('p.khatianLog', 'kl');
        $qb->innerJoin('kl.khatianVersion', 'kv');
        $qb->innerJoin('kv.khatian', 'k');
        $qb->where("sr.office IN(:offices)")->setParameter('offices', $this->offices);
        $qb->andWhere('sr.type = :type')->setParameter('type', 'PORCHA_REQUEST');
        $qb->andWhere('kl.entryOperator = :entryOperator')->setParameter('entryOperator', $this->user);
        $qb->andWhere("kl.khatianStatus IN ('READY_FOR_VERIFICATION', 'READY_FOR_COMPARISON', 'READY_FOR_APPROVAL') ");
        $qb->orderBy('p.id', 'desc');

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(p.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function getNewKhatianListByStatus($data, $status, $count = false) {

        $qb = $this->queryKhatians();
        $qb->andWhere('kl.khatianStatus = :status');
        $qb->setParameter('status', $status);

        $this->teamConstraintQuery($qb, $status, 'BATCH');

        switch ($status)
        {
            case 'READY_FOR_VERIFICATION': $qb->andWhere($qb->expr()->isNull('kl.verifier')); break;
            case 'READY_FOR_COMPARISON': $qb->andWhere($qb->expr()->isNull('kl.comparer')); break;
            case 'READY_FOR_APPROVAL': $qb->andWhere($qb->expr()->isNull('kl.approver')); break;
        }

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        $this->queryKhatiansOrderBy($qb, $this->queryParameters($data), $status);

        //echo $qb->getQuery()->getSQL();
        return $qb->getQuery()->getResult();
    }

    public function getSRNewKhatianListByStatus($data, $status, $count = false) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->innerJoin('p.serviceRequest', 'sr');
        $qb->leftJoin('p.khatianLog', 'kl');
        $qb->leftJoin('kl.khatianVersion', 'kv');
        $qb->leftJoin('kv.khatian', 'k');
        $qb->leftJoin('k.volume', 'v');
        $qb->where("sr.office IN(:offices)")->setParameter('offices', $this->offices);
        $qb->andWhere('sr.type = :type')->setParameter('type', 'PORCHA_REQUEST');
        $qb->andWhere('kl.khatianStatus = :status')->setParameter('status', $status);

        $this->teamConstraintQuery($qb, $status, 'APP');

        switch ($status)
        {
            case 'READY_FOR_VERIFICATION': $qb->andWhere($qb->expr()->isNull('kl.verifier')); break;
            case 'READY_FOR_COMPARISON': $qb->andWhere($qb->expr()->isNull('kl.comparer')); break;
            case 'READY_FOR_APPROVAL': $qb->andWhere($qb->expr()->isNull('kl.approver')); break;
        }

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(p.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function isWorkflowTeamEnabled() {

        /**@var Office $thisOffice*/
        $thisOffice = $this->office;
        /**@var OfficeSettings $thisOfficeSettings*/
        $thisOfficeSettings = $thisOffice->getOfficeSettings();
        return ($thisOfficeSettings) ? $thisOfficeSettings->isWorkflowTeam() : false;
    }

    public function getSRAppliedKhatians($data) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->innerJoin('p.serviceRequest', 'sr');
        $qb->leftJoin('p.khatianLog', 'kl');
        $qb->leftJoin('kl.khatianVersion', 'kv');
        $qb->leftJoin('kv.khatian', 'k');
        $qb->where('sr.office IN(:offices)')->setParameter('offices', array_values($this->offices));
        $qb->andWhere('sr.type = :type')->setParameter('type', 'PORCHA_REQUEST');
        $qb->andWhere("sr.status != 'DELIVERED'");

        $qb = $this->teamConstraintQuery($qb, 'ENTRY', 'APP');

        $qb->orderBy('sr.createdAt', 'desc');

        if (!empty($data)) {

            $params = $this->queryParameters($data);

            if (!empty($params['orderField'])) {
                $qb->orderBy($params['orderField'], $params['order']);
            }

            if (!empty($params['arrFilterField']['sr.createdAt'])) {
                $timestamp = strtotime($params['arrFilterField']['sr.createdAt']);
                $qb->andWhere('DAY(sr.createdAt) = :dd')->setParameter('dd', date('d', $timestamp));
                unset($params['arrFilterField']['sr.createdAt']);
            }

            if (!empty($params['arrFilterField']['kv.nonDeliverable'])) {
                if ($params['arrFilterField']['kv.nonDeliverable']) {
                    $qb->andWhere('kv.nonDeliverable != "" ');
                } else {
                    $qb->andWhere('kv.nonDeliverable = "" ');
                }
                unset($params['arrFilterField']['kv.nonDeliverable']);
            }

            $this->filterQuery($qb, $params['arrFilterField']);
            $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        }

        return $qb->getQuery();
    }


    public function getReAssignedKhatianList($data, $count = false) {

        $qb = $this->queryKhatians();

        $khatianStatus = '';
        if (in_array('ROLE_KHATIAN_ENTRY', $this->user->getRoles())) {
            $khatianStatus = 'CORRECTION_REQUIRED';
            $qb->andWhere('kl.entryOperator = :user');
        } else if (in_array('ROLE_KHATIAN_VERIFICATION', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_VERIFICATION';
            $qb->andWhere('kl.verifier = :user');
        } else if (in_array('ROLE_KHATIAN_COMPARISON', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_COMPARISON';
            $qb->andWhere('kl.comparer = :user');
        } else if (in_array('ROLE_KHATIAN_APPROVAL', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_APPROVAL';
            $qb->andWhere('kl.approver = :user');
        }

        $qb->andWhere('kl.khatianStatus = :status');
        $qb->setParameter('status', $khatianStatus);
        $qb->setParameter('user', $this->user);

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        $this->queryKhatiansOrderBy($qb, $this->queryParameters($data), 'CORRECTION_REQUIRED');

        return $qb->getQuery()->getResult();
    }

    public function getSRReAssignedKhatianList($data, $count = false) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->innerJoin('p.serviceRequest', 'sr');
        $qb->innerJoin('p.khatianLog', 'kl');
        $qb->innerJoin('kl.khatianVersion', 'kv');
        $qb->innerJoin('kv.khatian', 'k');
        $qb->where("sr.office IN(:offices)")->setParameter('offices', $this->offices);
        $qb->andWhere('sr.type = :type')->setParameter('type', 'PORCHA_REQUEST');

        $khatianStatus = '';
        if (in_array('ROLE_KHATIAN_ENTRY', $this->user->getRoles())) {
            $khatianStatus = 'CORRECTION_REQUIRED';
            $qb->andWhere('kl.entryOperator = :user');
        } else if (in_array('ROLE_KHATIAN_VERIFICATION', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_VERIFICATION';
            $qb->andWhere('kl.verifier = :user');
        } else if (in_array('ROLE_KHATIAN_COMPARISON', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_COMPARISON';
            $qb->andWhere('kl.comparer = :user');
        } else if (in_array('ROLE_KHATIAN_APPROVAL', $this->user->getRoles())) {
            $khatianStatus = 'READY_FOR_APPROVAL';
            $qb->andWhere('kl.approver = :user');
        }

        $qb->andWhere('kl.khatianStatus = :status');
        $qb->setParameter('status', $khatianStatus);
        $qb->setParameter('user', $this->user);

        if (!empty($data)) {
            $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('sr.estimateDeliveryAt', 'desc');
            $qb->addOrderBy('sr.urgency', 'desc');
        }

        if ($count) {
            $qb->select('COUNT(p.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function khatianListTitle($currentUser) {

        $title = 'খাতিয়ান তালিকা  ';
        if (in_array('ROLE_KHATIAN_ENTRY', $currentUser->getRoles())) {
            $title = 'সংশোধন  প্রয়োজন';
        } else if (in_array('ROLE_KHATIAN_VERIFICATION', $currentUser->getRoles())) {
            $title = 'খাতিয়ান তালিকা (পুনঃ যাচাইয়ের অপেক্ষায়)';
        } else if (in_array('ROLE_KHATIAN_COMPARISON', $currentUser->getRoles())) {
            $title = 'খাতিয়ান তালিকা (পুনঃ তুলনার অপেক্ষায়)';
        } else if (in_array('ROLE_KHATIAN_APPROVAL', $currentUser->getRoles())) {
            $title = 'খাতিয়ান তালিকা (অনুমোদনের  অপেক্ষায়)';
        }
        return $title;
    }

    private function queryKhatians() {

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->innerJoin('kl.khatianVersion', 'lv');
        $qb->innerJoin('lv.khatian', 'k');
        $qb->innerJoin('k.volume', 'v');
        $qb->innerJoin('v.upozila', 'u');
        $qb->innerJoin('v.survey', 's');
        $qb->innerJoin('k.mouza', 'm');
        $qb->innerJoin('k.jlnumber', 'j');
        $qb->where("k.office = :office");
        $qb->setParameter('office', $this->office);
        $qb->andWhere("kl.batch = '1' ");
        return $qb;
    }

    private function serviceRequestQueryKhatians() {

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->innerJoin('p.serviceRequest', 'sr');
        $qb->leftJoin('p.khatianLog', 'kl');
        $qb->leftJoin('kl.khatianVersion', 'lv');
        $qb->leftJoin('lv.khatian', 'k');
        $qb->leftJoin('k.volume', 'v');
        $qb->where("sr.office IN(:offices)")->setParameter('offices', $this->offices);
        $qb->andWhere('sr.type = :type')->setParameter('type', 'PORCHA_REQUEST');
        return $qb;
    }

    private function queryKhatiansOrderBy($qb, $params, $flag = '') {

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
//            $qb->orderBy('s.name', 'asc');
//            $qb->addOrderBy('u.name', 'asc');
//            $qb->addOrderBy('m.name', 'asc');
//            $qb->addOrderBy('k.canonicalKhatianNo', 'asc');

            switch ($flag) {
                case 'SENT_KHATIANS':
                case 'DRAFT':
                case 'CORRECTION_REQUIRED':
                case 'READY_FOR_VERIFICATION':
                    $qb->orderBy('kl.entryAt', 'asc');
                    break;
                case 'READY_FOR_COMPARISON':
                    $qb->orderBy('kl.verifiedAt', 'asc');
                    break;
                case 'READY_FOR_APPROVAL':
                    $qb->orderBy('kl.comparedAt', 'asc');
                    break;
                default:
                    $qb->orderBy('kl.entryAt', 'asc');

            }
        }
    }

    private function queryKhatiansParams($qb, $params) {

        $this->filterQuery($qb, $params['arrFilterField']);
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
    }

    private function teamConstraintQuery($qb, $status, $type = 'BATCH') {

        if (!$this->isWorkflowTeamEnabled()) {
            return $qb;
        }

        $rows = $this->findTeamsByStatus($status, $type);
        $data = $this->getTeamParams($rows);

        if (strtoupper($type) == 'APP') {
            return $this->appConstraintClauses($qb, $data);
        }

        return $this->batchConstraintClauses($qb, $data);
    }

    private function appConstraintClauses($qb, $data) {
        /**@var QueryBuilder $qb*/
        if (!empty($data['survey'])) {
            $qb->andWhere('p.survey IN (:surveys)')->setParameter('surveys', array_values($data['survey']));
        }
        if (!empty($data['upozila'])) {
            $qb->andWhere('p.upozila IN (:upozilas)')->setParameter('upozilas', array_values($data['upozila']));
        }
        if (!empty($data['mouza'])) {
            $qb->andWhere('p.mouza IN (:mouzas)')->setParameter('mouzas', array_values($data['mouza']));
        }
        return $qb;
    }

    private function batchConstraintClauses($qb, $data) {
        /**@var QueryBuilder $qb*/
        if (!empty($data['survey'])) {
            $qb->andWhere('v.survey IN (:surveys)')->setParameter('surveys', array_values($data['survey']));
        }
        if (!empty($data['upozila'])) {
            $qb->andWhere('v.upozila IN (:upozilas)')->setParameter('upozilas', array_values($data['upozila']));
        }
        if (!empty($data['mouza'])) {
            $qb->andWhere('k.mouza IN (:mouzas)')->setParameter('mouzas', array_values($data['mouza']));
        }
        return $qb;
    }

    private function findTeamsByRole($type = 'BATCH') {

        $qb = $this->em->getRepository('PorchaProcessingBundle:WorkflowTeam')->createQueryBuilder('w');

        if (in_array('ROLE_KHATIAN_ENTRY', $this->user->getRoles())) {
            $qb->innerJoin('w.entryOperators', 'e', 'WITH', 'e.id = :user');
            $qb->setParameter('user', $this->user);
        } else if (in_array('ROLE_KHATIAN_VERIFICATION', $this->user->getRoles())) {
            $qb->innerJoin('w.verifiers', 'v', 'WITH', 'v.id = :user');
            $qb->setParameter('user', $this->user);
        } else if (in_array('ROLE_KHATIAN_COMPARISON', $this->user->getRoles())) {
            $qb->innerJoin('w.comparers', 'c', 'WITH', 'c.id = :user');
            $qb->setParameter('user', $this->user);
        } else if (in_array('ROLE_KHATIAN_APPROVAL', $this->user->getRoles())) {
            $qb->innerJoin('w.approvers', 'a', 'WITH', 'a.id = :user');
            $qb->setParameter('user', $this->user);
        } else {
            $qb->innerJoin('w.entryOperators', 'e', 'WITH', 'e.id = :user');
            $qb->setParameter('user', $this->user);
        }

        $qb->where('w.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('w.type = :type')->setParameter('type', strtoupper($type));
        return $qb->getQuery()->getResult();
    }

    private function findTeamsByStatus($status, $type) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:WorkflowTeam')->createQueryBuilder('w');

        switch (strtoupper($status)) {

            case 'READY_FOR_VERIFICATION':
                $qb->innerJoin('w.verifiers', 'v', 'WITH', 'v.id = :user');
                $qb->setParameter('user', $this->user);
                break;
            case 'READY_FOR_COMPARISON':
                $qb->innerJoin('w.comparers', 'c', 'WITH', 'c.id = :user');
                $qb->setParameter('user', $this->user);
                break;
            case 'READY_FOR_APPROVAL':
                $qb->innerJoin('w.approvers', 'a', 'WITH', 'a.id = :user');
                $qb->setParameter('user', $this->user);
                break;
            case 'DRAFT':
            case 'ENTRY':
                $qb->innerJoin('w.entryOperators', 'e', 'WITH', 'e.id = :user');
                $qb->setParameter('user', $this->user);
                break;
        }

        $qb->where('w.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('w.type = :type')->setParameter('type', strtoupper($type));
        return $qb->getQuery()->getResult();
    }

    private function getTeamParams($rows) {

        $data = array();

        if (count($rows) < 1) {
            $data['survey'][] = 0;
            $data['upozila'][] = 0;
            $data['mouza'][] = 0;
            return $data;
        }

        foreach ($rows as $row) {

            /**@var WorkflowTeam $row */

            if ($row->getSurveys()) {
                foreach ($row->getSurveys() as $survey) {
                    $data['survey'][] = $survey->getId();
                }
            }

            if ($row->getUpozilas()) {
                foreach ($row->getUpozilas() as $upozila) {
                    $data['upozila'][] = $upozila->getId();
                }
            }

            if ($row->getMouzas()) {
                foreach ($row->getMouzas() as $mouza) {
                    $data['mouza'][] = $mouza->getId();
                }
            }
        }

        return $data;
    }

    public function isPageViewableByWorkflowTeams(Volume $volume, Mouza $mouza, $type = 'BATCH') {

        if (!$this->isWorkflowTeamEnabled()) {
            return true;
        }

        $rows = $this->findTeamsByRole($type);
        $data = $this->getTeamParams($rows);

        $matchSurvey = true;
        $matchUpozila = true;
        $matchMouza = true;

        if (isset($data['survey'])) {
            $matchSurvey = (in_array($volume->getSurvey()->getId(), array_values($data['survey'])));
        }
        if (isset($data['upozila'])) {
            $matchUpozila = (in_array($volume->getUpozila()->getId(), array_values($data['upozila'])));
        }

        if (isset($data['mouza'])) {
            $matchMouza = (in_array($mouza->getId(), array_values($data['mouza'])));
        }

        return ($matchSurvey && $matchUpozila && $matchMouza) ? true : false;
    }

    public function isWorkflowPageViewableByKhatianLog(KhatianLog $khatianLog, $type = 'BATCH') {

        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        if ($this->isKhatianUser($khatianLog)) {
            return true;
        }

        $rows = $this->findTeamsByRole($type);
        $data = $this->getTeamParams($rows);

        $matchSurvey = true;
        $matchUpozila = true;
        $matchMouza = true;

        if (isset($data['survey'])) {
            $matchSurvey = (in_array($khatian->getVolume()->getSurvey()->getId(), array_values($data['survey'])));
        }
        if (isset($data['upozila'])) {
            $matchUpozila = (in_array($khatian->getVolume()->getUpozila()->getId(), array_values($data['upozila'])));
        }
        if (isset($data['mouza'])) {
            $matchMouza = (in_array($khatian->getMouza()->getId(), array_values($data['mouza'])));
        }

        return ($matchSurvey && $matchUpozila && $matchMouza) ? true : false;
    }

    private function isKhatianUser(KhatianLog $khatianLog) {

        if ($khatianLog->getEntryOperator() == $this->user || $khatianLog->getVerifier() == $this->user
            || $khatianLog->getComparer() == $this->user || $khatianLog->getApprover() == $this->user) {
            return true;
        }
        return false;
    }

    private function resetCorrectionMessage($khatianVersion)
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianCorrectionLog')->createQueryBuilder('c');
        $qb->select('c');
        $qb->join('c.khatianPage', 'p');
        $qb->where('p.khatianVersion = :khatianVersion')->setParameter('khatianVersion', $khatianVersion);
        $correctionMessages = $qb->getQuery()->getResult();

        /** @var KhatianCorrectionLog $correctionMessage */
        foreach ($correctionMessages as $correctionMessage) {
            $correctionMessage->setUser(null);
            $correctionMessage->setMessage(null);
            $this->em->persist($correctionMessage);
        }

        $this->em->flush();
    }

    public function archiveKhatian($khatianIds, User $user)
    {
        $khatianRepo = $this->em->getRepository('PorchaProcessingBundle:Khatian');
        $userOfficeId = $user->getOffice()->getId();
        foreach ($khatianIds as $khatianId) {
            $khatian = $khatianRepo->find($khatianId);

            if (!$khatian || $khatian->getOffice()->getId() != $userOfficeId || $khatian->getStatus() != 'APPROVED') {
                return false;
            }

            $khatian->setStatus('ARCHIVED');
            $this->em->persist($khatian);
        }

        $this->em->flush();

        return true;
    }

    public function srMoveToVerification(KhatianLog $khatianLog) {

        $khatianLog->setEntryOperator($this->user);
        $khatianLog->setEntryAt(new \DateTime());
        $khatianLog->setKhatianStatus('READY_FOR_VERIFICATION');
        $this->em->persist($khatianLog);
        $this->em->flush();
    }

    public function getSRReadyForDeliveryKhatians($data, $count = false) {

        $qb = $this->serviceRequestQueryKhatians();
        $qb->andWhere("sr.status = 'READY_FOR_DELIVERY' ");

        if (!empty($data)) {
            $qb = $this->queryKhatiansParams($qb, $this->queryParameters($data));
        }

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function moveKhatiansToNextStep($selectedAction, $ids) {

        if (count($ids) < 1) {
            return;
        }

        foreach ($ids as $key=>$val) {

            $khatianLog = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->find($key);
            $this->srWorkflowAction($khatianLog, $selectedAction, null, $this->user);

            $this->em->persist($khatianLog);
        }
        $this->em->flush();
    }

    public function getWorkflowTeams($type) {

        return $this->em->getRepository('PorchaProcessingBundle:WorkflowTeam')->findBy(array('office' => $this->office, 'type' => strtoupper($type)));
    }

    public function saveEntity($entity) {
        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    public function createWorkflowTeam(WorkflowTeam $workflowTeam, $type) {

        $workflowTeam->setOffice($this->office);
        $workflowTeam->setActive(true);
        $workflowTeam->setType(strtoupper($type));
        $this->em->persist($workflowTeam);
        $this->em->flush($workflowTeam);
    }

    public function updateWorkflowTeam(WorkflowTeam $workflowTeam) {
        $this->saveEntity($workflowTeam);
    }

    public function draftKhatianCount($sr = false) {
        return ($sr) ? $this->getSRDraftKhatians(null, true) : $this->getDraftKhatians(null, true);
    }

    public function sentKhatianCount($sr = false) {
        return ($sr) ? $this->getSRSentKhatianList(null, true) : $this->getSentKhatianList(null, true);
    }

    public function reAssignedKhatianCount($sr = false) {
        return ($sr) ? $this->getSRReAssignedKhatianList(null, true) : $this->getReAssignedKhatianList(null, true);
    }

    public function verifyNewKhatianCount($sr = false) {
        return ($sr) ? $this->getSRNewKhatianListByStatus(null, 'READY_FOR_VERIFICATION', true) : $this->getNewKhatianListByStatus(null, 'READY_FOR_VERIFICATION', true);
    }

    public function compareNewKhatianCount($sr = false) {
        return ($sr) ? $this->getSRNewKhatianListByStatus(null, 'READY_FOR_COMPARISON', true) : $this->getNewKhatianListByStatus(null, 'READY_FOR_COMPARISON', true);
    }

    public function approveNewKhatianCount($sr = false) {
        return ($sr) ? $this->getSRNewKhatianListByStatus(null, 'READY_FOR_APPROVAL', true) : $this->getNewKhatianListByStatus(null, 'READY_FOR_APPROVAL', true);
    }

    public function approvedKhatianCount($sr = false) {
        return ($sr) ? $this->getSRNewKhatianListByStatus(null, 'APPROVED', true) : $this->getNewKhatianListByStatus(null, 'APPROVED', true);
    }
}