<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Entity\Office;
use AppBundle\Traits\EntityAssistant;
use AppBundle\Traits\QueryAssistant;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianCorrectionLog;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianPage;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\OfficeTemplate;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Generator\IdGenerator;
use PorchaProcessingBundle\Util\PlaceHolders;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class KhatianManager
{
    use QueryAssistant;
    use EntityAssistant;

    protected $em;
    protected $workflowManager;
    protected $user = null;
    protected $office = null;
    protected $offices = array();

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, WorkflowManager $workflowManager) {
        $this->workflowManager = $workflowManager;
        $this->em = $entityManager;
            if ($tokenStorage->getToken()) {
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
    }

    public function getKhatianFirstPageByKhatianLog($khatianLog) {

        $khatianVersion = $khatianLog->getKhatianVersion();
        return $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findOneBy(array(
            'khatianVersion' => $khatianVersion,
            'type' => 'PAGE1',
        ));
    }

    public function getKhatianFirstPage(Khatian $khatian) {

        return $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findOneBy(array(
            'khatianVersion' => $khatian->getLastVersion(),
            'type' => 'PAGE1',
        ));
    }

    public function deleteKhatianPage(KhatianPage $khatianPage) {

        $khatian = $khatianPage->getKhatianVersion()->getKhatian();
        $khatianStatus = strtoupper($khatian->getStatus());
        $khatianPageType = strtoupper($khatianPage->getType());

        if ($khatianStatus == 'APPROVED' || $khatianStatus == 'ARCHIVED') {
            return false;
        }

        if ($khatianPageType == 'PAGE2') {
            $row = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findOneBy(array(
                'khatianVersion' => $khatianPage->getKhatianVersion(),
                'type' => 'PAGE2_ADDITIONAL'
            ));
            if ($row) {
                return false;
            }
        }

        if ($khatianPageType != 'PAGE1') {
            $this->em->remove($khatianPage);
            $this->em->remove($khatianPage->getCorrectionLog());
            $this->em->flush();
            return true;
        }

        return false;
    }

    public function deleteKhatian(Khatian $khatian) {

        $khatianVersion = $khatian->getLastVersion();

        if (($khatian->getStatus() == 'DRAFT' || $khatian->getStatus() == 'NONE')  && $khatianVersion->getVerifier() == null) {

            if ($khatianVersion->getEntryOperator() == $this->user) {

                $khatian->setVolumeTemplate(null);
                $khatian->setLastVersion(null);

                $versions = $this->em->getRepository('PorchaProcessingBundle:KhatianVersion')->findBy(array('khatian' => $khatian));
                foreach($versions as $version) {

                    $pages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $version));
                    foreach ($pages as $page) {
                        $this->em->remove($page);
                        if ($page->getCorrectionLog()) {
                            $this->em->remove($page->getCorrectionLog());
                        }
                    }

                    $this->em->remove($version);
                    $this->em->flush();
                }

                $this->em->remove($khatian);
                $this->em->flush();
                return true;
            }
        }

        return false;
    }

    public function deleteNoneStatusKhatian(Volume $volume) {



//        $khatianVersion = $khatian->getLastVersion();
//
//        if ($khatian->getStatus() == 'NONE' && $khatianVersion->getVerifier() == null) {
//
//            $khatian->setVolumeTemplate(null);
//            $khatian->setLastVersion(null);
//
//            $versions = $this->em->getRepository('PorchaProcessingBundle:KhatianVersion')->findBy(array('khatian' => $khatian));
//            foreach($versions as $version) {
//
//                $pages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $version));
//                foreach ($pages as $page) {
//                    $this->em->remove($page);
//                    if ($page->getCorrectionLog()) {
//                        $this->em->remove($page->getCorrectionLog());
//                    }
//                }
//
//                $this->em->remove($version);
//                $this->em->flush();
//            }
//
//            $this->em->remove($khatian);
//            $this->em->flush();
//            return true;
//
//        }

        return false;
    }

    public function createKhatianPages(Khatian $khatian, $type, $templateId) {

        $existingPage = null;

        if (strtoupper($type) == 'PAGE1' || strtoupper($type) == 'PAGE2' ) {
            $lastVersion = $khatian->getLastVersion();
            $existingPage = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findOneBy(array(
                'khatianVersion' => $lastVersion,
                'type' => $type,
            ));
            if ($existingPage) {
                return array('status' => false, 'khatianPage' => $existingPage);
            }
        }

        $khatianPage = new KhatianPage();
        $khatianPage->setKhatianVersion($khatian->getLastVersion());
        $khatianPage->setType($type);
        $khatianPage->setPageOrder($this->getKhatianPageOrder($type));
        $template = $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->find($templateId);
        $khatianPage->setOfficeTemplate($template);
        $this->em->persist($khatianPage);

        if (!$khatianPage->getCorrectionLog()) {
            $correctionLog = new KhatianCorrectionLog($khatianPage, null);
            $this->em->persist($correctionLog);
        }

        $this->em->flush();

        return array('status' => true, 'khatianPage' => $khatianPage);
    }

    public function getKhatianPageOrder($type) {

        switch ($type) {
            case 'PAGE1':
                return 1;
            case 'PAGE1_ADDITIONAL':
                return 2;
            case 'PAGE2':
                return 3;
            case 'PAGE2_ADDITIONAL':
                return 4;
        }
        return 0;
    }

    public function singleKhatianPages(KhatianLog $khatianLog) {

        $khatianVersion = $khatianLog->getKhatianVersion();
        $khatianPages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(
            array('khatianVersion' => $khatianVersion),
            array('id' => 'asc', 'pageOrder' => 'asc')
        );

        $pages = array();
        $page1Additionals = array();
        $page2Additionals = array();

        foreach ($khatianPages as $page) {
            switch ($page->getType()) {
                case 'PAGE1':
                    $pages['PAGE1'] = $page->getId();
                    break;
                case 'PAGE2':
                    $pages['PAGE2'] = $page->getId();
                    break;
                case 'PAGE1_ADDITIONAL':
                    $page1Additionals[] = $page->getId();
                    break;
                case 'PAGE2_ADDITIONAL':
                    $page2Additionals[] = $page->getId();
                    break;
            }
        }

        if (!empty($page1Additionals)) {
            $pages['PAGE1_ADDITIONAL'] = $page1Additionals;
        }
        if (!empty($page2Additionals)) {
            $pages['PAGE2_ADDITIONAL'] = $page2Additionals;
        }

        return $pages;
    }

    public function singleKhatianPagesByKhatian(Khatian $khatian) {

        $khatianVersion = $khatian->getLastVersion();
        $khatianPages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(
            array('khatianVersion' => $khatianVersion),
            array('id' => 'asc', 'pageOrder' => 'asc')
        );

        $pages = array();
        $page1Additionals = array();
        $page2Additionals = array();

        foreach ($khatianPages as $page) {
            switch (strtoupper($page->getType())) {
                case 'PAGE1':
                    $pages['PAGE1'] = $page->getId();
                    break;
                case 'PAGE2':
                    $pages['PAGE2'] = $page->getId();
                    break;
                case 'PAGE1_ADDITIONAL':
                    $page1Additionals[] = $page->getId();
                    break;
                case 'PAGE2_ADDITIONAL':
                    $page2Additionals[] = $page->getId();
                    break;
            }
        }

        if (!empty($page1Additionals)) {
            $pages['PAGE1_ADDITIONAL'] = $page1Additionals;
        }
        if (!empty($page2Additionals)) {
            $pages['PAGE2_ADDITIONAL'] = $page2Additionals;
        }

        return $pages;
    }

    public function getKhatianPages(KhatianVersion $khatianVersion) {
        return $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $khatianVersion), array('pageOrder' => 'asc', 'id' => 'asc'));
    }

    public function getKhatianLogByKhatianVersion(KhatianVersion $khatianVersion) {
        return $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->findOneBy(array('khatianVersion' => $khatianVersion), array('id' => 'desc'));
    }

    public function allowedToForward(Khatian $khatian) {

        $lastVersion = $khatian->getLastVersion();
        $row = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findOneBy(array('khatianVersion' => $lastVersion, 'entryComplete' => 0));
        return empty($row);
    }

    public function khatianEntryValidate($requestAll, $khatianAction, $khatianLogId, $khatianNo) {

        if (in_array(strtoupper($khatianAction), array('PAGE1_ADDITIONAL', 'PAGE2', 'PAGE2_ADDITIONAL', 'PAGE_SAVE'))) {
            return array('status' => true, 'message' => '');
        }

        $blankMsg = '';
        $error = false;


        if (isset($requestAll['khatian_page']['khatian_no']) && empty($requestAll['khatian_page']['khatian_no'])) {
            $blankMsg .= 'খাতিয়ান নং,';
            $error = true;
        }

        if (isset($requestAll['khatian_page']['district']) && empty($requestAll['khatian_page']['district'])) {
            $blankMsg .= 'জেলা ';
            $error = true;
        }

        if (isset($requestAll['khatian_page']['upozila']) && empty($requestAll['khatian_page']['upozila'])) {
            $blankMsg .= 'থানা /উপজেলা ';
            $error = true;
        }

        if (isset($requestAll['khatian_page']['mouza']) && empty($requestAll['khatian_page']['mouza'])) {
            $blankMsg .= 'মৌজা ,';
            $error = true;
        }

        if (isset($requestAll['khatian_page']['jl_no']) && empty($requestAll['khatian_page']['jl_no'])) {
            $blankMsg .= 'জে এল নং,';
            $error = true;
        }

        if ($error) {
            return array('status' => false, 'message' => $blankMsg);
        }

        return $this->checkKhatianNoExists($khatianLogId, $requestAll['khatian_page']['khatian_no'], $khatianNo);
    }

    private function isKhatianInVolume($khatianLogId, $khatianNo) {

        $khatianLog = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->find($khatianLogId);
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();
        /**@var Volume $volume */
        $volume = $khatian->getVolume();
        $khatianNo = $this->convertNumber('bn2en', $khatianNo);

        $volumeMouzas = $volume->getVolumeMouzas();
        foreach ($volume->getVolumeMouzas() as $volumeMouza) {
            if ($volumeMouza->getStartKhatianNo() <= $khatianNo && $volumeMouza->getEndKhatianNo() >= $khatianNo) {
                return true;
            }
        }

        return false;
    }

    public function checkKhatianNoExists($khatianLogId, $khatianNo, $except = '') {

        $khatianNoEn = $this->convertNumber('bn2en', $khatianNo);
        preg_match_all('!\d+!', $khatianNoEn, $matches);
        $khatianNoEn = implode("", $matches[0]);

        $khatianLog = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->find($khatianLogId);
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();
        $volume = $khatian->getVolume();
        $mouza = $khatian->getMouza();
        /**@var Volume $volume*/
        $volumeMouzas = $volume->getVolumeMouzas();

        foreach ($volumeMouzas as $vm) {
            if ($vm->getMouza()->getId() == $mouza->getId()) {
                if ($khatianNoEn >= $vm->getStartKhatianNo() && $khatianNoEn <= $vm->getEndKhatianNo()) {

                    $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
                    $qb->where('k.volume = :volume')->setParameter('volume', $volume);
                    $qb->andWhere('k.office = :office')->setParameter('office', $this->office);
                    $qb->andWhere('k.mouza = :mouza')->setParameter('mouza', $vm->getMouza());
                    $qb->andWhere('k.khatianNo = :khatiannNo')->setParameter('khatiannNo', $khatianNo);
                    if (!empty($except)) {
                        $qb->andWhere('k.khatianNo != :except');
                        $qb->setParameter('except', $except);
                    }
                    $qb->setMaxResults(1);
                    if ($qb->getQuery()->getOneOrNullResult()) {
                        return array('status' => false, 'message' => 'খতিয়ান নং উপস্থিত ');
                    }
                    return array('status' => true, 'message' => 'success');
                }
                return array('status' => false, 'message' => 'এই খতিয়ান নং ভলিউমের খতিয়ান ব্যাপ্তির মধ্যে পড়েনা ');
            }
        }
        return array('status' => false, 'message' => 'ভলিউমের মৌজা অনুপস্থিত ');
    }

    public function updateKhatianPage(KhatianPage $khatianPage, $requestAll) {

        $khatian = $khatianPage->getKhatianVersion()->getKhatian();

        if (strtoupper($khatianPage->getType()) == 'PAGE1') {

            $khatian->setKhatianNo($requestAll['khatian_page']['khatian_no']);

            if (isset($requestAll['khatian_page']['rs_no'])) {
                $khatian->setRsNo($requestAll['khatian_page']['rs_no']);
            }
            if (isset($requestAll['khatian_page']['pargana'])) {
                $khatian->setPargana($requestAll['khatian_page']['pargana']);
            }
            if (isset($requestAll['khatian_page']['taugi_no'])) {
                $khatian->setTaugiNo($requestAll['khatian_page']['taugi_no']);
            }
        }

        $khatianPage->setentryComplete(true);

        if (!$khatianPage->getCorrectionLog()) {
            $correctionLog = new KhatianCorrectionLog($khatianPage, null);
            $this->em->persist($correctionLog);
        }

        $this->em->persist($khatian);

        $this->em->persist($khatianPage);
        $this->em->flush();
    }

    public function changeEntryTemplate(KhatianPage $khatianPage, OfficeTemplate $officeTemplate) {

        if ($khatianPage->getKhatianVersion()->getKhatian()->getOffice() != $this->office) {
            return false;
        }
        if (!$this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array('office' => $this->office, 'id' => $officeTemplate->getId()))) {
            return false;
        }

        $khatianPage->setOfficeTemplate($officeTemplate);
        $this->em->persist($khatianPage);
        $this->em->flush();
        return true;
    }

    public function srUpdateKhatian($khatianLog, $workflowAction, $requestAll, $serviceRequestId) {

        $khatianVersion = $khatianLog->getKhatianVersion();
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        if (!empty($requestAll['non-deliverable'])) {
            $khatianVersion->setNonDeliverable($requestAll['remark-non-deliverable']);
        } else {
            $khatianVersion->setNonDeliverable(null);
        }

        $noneStatus = $khatianLog->getKhatianStatus();

        if ($noneStatus == 'NONE') {

            $volume = $khatian->getVolume();
            /**@var Volume $volume */
            $volume->setNoEntryKhatianCount($volume->getNoEntryKhatianCount() - 1);
            $this->em->persist($volume);
        }

        if ($workflowAction == 'READY_FOR_VERIFICATION') {
            $khatianLog->setKhatianStatus('READY_FOR_VERIFICATION');
            $khatianLog->setEntryAt(new \DateTime());

            $this->em->persist($khatianVersion);
        } else {
            $khatianLog->setKhatianStatus('DRAFT');
        }

        $khatian->setMouzaMapReference($requestAll['khatian_page']['mouzaMapReference']);

        $this->em->persist($khatian);
        $this->em->persist($khatianVersion);
        $this->em->persist($khatianLog);
        $this->em->flush();

        $this->setAppliedKhatianId($khatian, $khatianLog, $serviceRequestId);
    }

    public function setAppliedKhatianId(Khatian $khatian, KhatianLog $khatianLog, $serviceRequestId) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->createQueryBuilder('p');
        $qb->join('p.serviceRequest', 'sr');
        $qb->where('sr.office IN(:offices)')->setParameter('offices', $this->offices);
        $qb->andWhere("sr.status = 'PENDING' ");
        $qb->andWhere('p.survey = :survey')->setParameter('survey', $khatian->getVolume()->getSurvey());
        $qb->andWhere('p.district = :district')->setParameter('district', $khatian->getVolume()->getDistrict());
        $qb->andWhere('p.upozila = :upozila')->setParameter('upozila', $khatian->getVolume()->getUpozila());
        $qb->andWhere('p.mouza = :mouza')->setParameter('mouza', $khatian->getMouza());
        $qb->andWhere('p.khatianNo = :khatianNo')->setParameter('khatianNo', $khatian->getKhatianNo());
        $qb->andWhere($qb->expr()->isNull('p.khatianLog'));
        $qb->addOrderBy('p.id', 'asc');
        $rows = $qb->getQuery()->getResult();

        if ($rows) {
            $i = 1;
            foreach ($rows as $row) {

                /**@var ServiceRequestPorcha $row*/

                if ($row->getServiceRequest()->getId() == $serviceRequestId) {
                    $row->setKhatianLog($khatianLog);
                    $this->em->persist($row);

                    $serviceRequest = $row->getServiceRequest();
                    $serviceRequest->setStatus('PROCESSING');
                    $this->em->persist($serviceRequest);
                } else {
                    $newKhatianLog = new KhatianLog();
                    $newKhatianLog->setKhatianVersion($khatianLog->getKhatianVersion());
                    $newKhatianLog->setKhatianStatus('HAS_ENTRY');
                    $this->em->persist($newKhatianLog);
                    $this->em->flush();

                    $row->setKhatianLog($newKhatianLog);
                    $this->em->persist($row);

                    $serviceRequest = $row->getServiceRequest();
                    $serviceRequest->setStatus('PROCESSING');
                    $this->em->persist($serviceRequest);
                }

                $this->em->flush();
                $i++;
            }
        }
    }

    public function getKhatianList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->innerJoin('k.volume', 'v');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

    public function lock(KhatianLog $khatianLog, $user)
    {
        if (in_array($khatianLog->getKhatianStatus(), array('READY_FOR_VERIFICATION', 'READY_FOR_COMPARISON', 'READY_FOR_APPROVAL', 'APPROVED'))) {
            $khatianLog->setLockedBy($user);
            $khatianLog->setLockedAt(new \DateTime());

            $this->update($khatianLog);
        }
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function getKhatianStatisticsByStatus()
    {
        return $this->em->getRepository('PorchaProcessingBundle:Khatian')->getKhatianStatisticsByStatus();
    }

    public function getKhatianStatusNames($except = array())
    {
        $ret = array(
            'DRAFT' => array('color' => 'blue-madison', 'title' => 'খসড়া '),
            'CORRECTION_REQUIRED' => array('color' => 'red-intense', 'title' => ''),
            'READY_FOR_VERIFICATION' => array('color' => 'green-haze', 'title' => ''),
            'READY_FOR_COMPARISON' => array('color' => 'purple-plum', 'title' => ''),
            'READY_FOR_APPROVAL' => array('color' => 'blue-madison', 'title' => ''),
            'APPROVED' => array('color' => 'red-intense', 'title' => ''),
            'ARCHIVED' => array('color' => 'green-haze', 'title' => ''),
        );

        foreach ($except as $ex) {
            unset($ret[$ex]);
        }
        return $ret;
    }

    public function getKhatianPagePrintView($khatianPages, $khatian)
    {
        $khatianRepo = $this->em->getRepository('PorchaProcessingBundle:Khatian');
        $khatianPageRepo = $this->em->getRepository('PorchaProcessingBundle:KhatianPage');

        $page1 = ''; $page2 = ''; $page1Additional = array(); $page2Additional = array();

        foreach ($khatianPages as $kp) {

            $rp = array(
                'template' => $kp->getOfficeTemplate()->getTemplate(),
                'data' => array_merge($khatianRepo->mappedByField($khatian), $khatianPageRepo->mappedByField($kp)),
            );

            if ($kp->getType() == 'PAGE1') {
                $page1 = $rp;
            } else if ($kp->getType() == 'PAGE2') {
                $page2 = $rp;
            } else if ($kp->getType() == 'PAGE1_ADDITIONAL') {
                $page1Additional[] = $rp;
            } else if ($kp->getType() == 'PAGE2_ADDITIONAL') {
                $page2Additional[] = $rp;
            }
        }

        $reOrder = array();

        if (!empty($page1)) {
            $reOrder[] = $page1;
        }
        if (!empty($page2)) {
            $reOrder[] = $page2;
        }

        if (count($page1Additional) > count($page2Additional)) {
            $additional = $page1Additional;
        } else {
            $additional = $page2Additional;
        }

        for ($i = 0; $i < count($additional); $i++) {
            $reOrder[] = (isset($page1Additional[$i])) ? $page1Additional[$i] : array('data' => null);
            $reOrder[] = (isset($page2Additional[$i])) ? $page2Additional[$i] : array('data' => null);
        }

        return $reOrder;
    }

    public function getKhatianPageTemplate(KhatianPage $khatianPage) {

        $officeTemplate = $khatianPage->getOfficeTemplate();

        $templateId = $officeTemplate->getTemplate();
        return $templateId;

        $type = $khatianPage->getType();

        switch ($type) {
            case 'PAGE1_ADDITIONAL':
                $templateId = $khatianPage->getKhatianVersion()->getKhatian()->getVolumeTemplate()->getTemplatePage1Additional();
                break;
            case 'PAGE2':
                $templateId = $khatianPage->getKhatianVersion()->getKhatian()->getVolumeTemplate()->getTemplatePage2();
                break;
            case 'PAGE2_ADDITIONAL':
                $templateId = $khatianPage->getKhatianVersion()->getKhatian()->getVolumeTemplate()->getTemplatePage2Additional();
                break;
            default:
                $officeTemplate = $khatianPage->getOfficeTemplate();
                $templateId = $officeTemplate->getTemplate();
        }

        return $templateId;
    }

    public function prepareKhatianPagePaginationForPrintView($khatianPages)
    {
        $pagination = array(); array('PAGE1' => array(), 'PAGE1_ADDITIONAL' => array(), 'PAGE2' => array(), 'PAGE2_ADDITIONAL' => array());
        $pageNumber = array('PAGE1_ADDITIONAL' => 0, 'PAGE2_ADDITIONAL' => 0);

        foreach ($khatianPages as $khatianPage) {
            $k = $khatianPage['data'];
            if ($k) {

                $attr = array('id' => $k['id'], 'label' => $k['page_type'], 'type' => $k['page_type'], 'disabled' => false);

                switch ($k['page_type']) {
                    case 'PAGE1':
                    case 'PAGE2':
                        $pagination[$k['page_type']] = $attr;
                        break;
                    case 'PAGE1_ADDITIONAL':
                    case 'PAGE2_ADDITIONAL':
                        if (!array_key_exists($k['page_type'], $pagination)) {
                            $pagination[$k['page_type']] = array_merge($attr, array(
                                'disabled' => true,
                                'label' => $attr['label'] . ' >>'
                            ));
                            $pagination[$k['page_type']]['id'] = false;
                        }

                        $pageNumber[$k['page_type']]++;
                        $attr['label'] = $pageNumber[$k['page_type']];
                        $pagination[] = $attr;
                }
            }
        }

        if (empty($pagination['PAGE1_ADDITIONAL'])) {
            //unset($pagination['PAGE1_ADDITIONAL']);
            //$pagination['PAGE1']['disabled'] = true;
        }
        if (empty($pagination['PAGE2_ADDITIONAL'])) {
            //unset($pagination['PAGE2']);
            //unset($pagination['PAGE2_ADDITIONAL']);
        }
//echo count($khatianPages);
        return $pagination;
    }

    public function getNonDeliverableMessages($messageIds) {

        if (!is_array($messageIds)) {
            return null;
        }
        $qb = $this->em->getRepository('PorchaProcessingBundle:NonDeliverableMessage')->createQueryBuilder('nd');
        $qb->where('nd.id IN (:ids)')->setParameter('ids', array_values($messageIds));
        return $qb->getQuery()->getResult();
    }

    public function getAllNonDeliverableMessages() {

        return $this->em->getRepository('PorchaProcessingBundle:NonDeliverableMessage')->findAll();
    }

    public function recordCountByDate($date, $office)
    {
        $khatianVersionRepo = $this->em->getRepository('PorchaProcessingBundle:KhatianVersion');
        return $khatianVersionRepo->recordCountByDate($date, $office);
    }

    public function attachKhatianPageData(KhatianPage $khatianPage, Khatian $khatian, $requestAll) {

        $data = $requestAll['khatian_page'];
        $khatian->setKhatianNo(isset($data['khatian_no']) ? $data['khatian_no'] : '');
        $khatian->setRsNo(isset($data['rs_no']) ? $data['rs_no'] : '');
        $khatian->setTaugiNo(isset($data['taugi_no']) ? $data['taugi_no'] : '');
        $khatian->setPargana(isset($data['pargana']) ? $data['pargana'] : '');

        return  array(
            'khatianPage' => $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->mappedByField1($khatianPage, $data),
            'khatian' => $khatian
        );
    }

    public function applicationExists($applicationId) {

        $row = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->findOneBy(array(
            'id' => $applicationId,
            'isDelivered' => 0,
        ));

        return ($row) ? true : false;
    }

    public function sendAppKhatianToVerification($applicationId, $khatianId) {

        $khatian = $this->em->getRepository('PorchaProcessingBundle:Khatian')->find($khatianId);

//        $qb = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->createQueryBuilder('r');
//        $qb->join('r.khatians', 'k');
//        $qb->where('r.id = :appId');
//        $qb->setParameter('appId', $applicationId);
//        $qb->andWhere('k.id = :khatian');
//        $qb->setParameter('khatian', $khatian);
//        if ($qb->getQuery()->getOneOrNullResult() !== null) {
//            return false;
//        }

        if (strtoupper($khatian->getStatus()) == 'APPROVED' && $khatian->isArchived()) {

            $lastVersion = $khatian->getLastVersion();

            $khatianVersion = new KhatianVersion();
            $khatianVersion->setKhatian($khatian);
            $khatianVersion->setVersionNo($lastVersion->getVersionNo());
            $khatianVersion->setEntryOperator($this->user);
            $khatianVersion->setEntryAt(new \DateTime());
            $this->em->persist($khatianVersion);

            $rows = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $lastVersion));
            foreach ($rows as $row) {
                $row->setKhatianVersion($khatianVersion);
                $this->em->persist($khatianVersion);
            }
            $khatian->setLastVersion($khatianVersion);
            $khatian->setStatus('READY_FOR_VERIFICATION');

            $this->em->flush();
        }

        $serviceRequestPorcha = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findOneBy(array(
            'serviceRequest' => $applicationId,
            'survey' => $khatian->getVolume()->getSurvey(),
            'district' => $khatian->getVolume()->getDistrict(),
            'upozila' => $khatian->getVolume()->getUpozila(),
            'mouza' => $khatian->getMouza(),
            'khatianNo' => $khatian->getKhatianNo()

        ));

        if (!$serviceRequestPorcha) {
            return false;
        }

        $serviceRequestPorcha->setKhatian($khatian);
        $this->em->persist($serviceRequestPorcha);
        $this->em->flush();
        return true;
    }

    public function applicationkhatianExists($requestAll) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->join('k.volume', 'v');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);

        $qb->andWhere('k.status NOT IN (:status)');
        $qb->setParameter('status', array_values(array('NONE', 'DRAFT')));

        $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->find($requestAll['mouzaId']);
        $qb->andWhere('k.mouza = :mouza');
        $qb->setParameter('mouza', $mouza);

        $jlnumber = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->find($requestAll['jlnumberId']);
        $qb->andWhere('k.jlnumber = :jlnumber');
        $qb->setParameter('jlnumber', $jlnumber);

        if (!empty($requestAll['thanaId'])) {
            $qb->andWhere('v.thana = :thana');
            $qb->setParameter('thana', $requestAll['thanaId']);
        } else {
            $qb->andWhere('v.upozila = :upozila');
            $qb->setParameter('upozila', $requestAll['upozilaId']);
        }

        $qb->andWhere('k.khatianNo = :khatianNo');
        $qb->setParameter('khatianNo', $requestAll['khatianNo']);
        $row = $qb->getQuery()->getResult();

        return ($row) ? $row[0]->getId() : 0;
    }

    public function khatianVolumes(ServiceRequestPorcha $serviceRequestPorcha) {

        $khatianNo = $this->convertNumber('bn2en', $serviceRequestPorcha->getKhatianNo());

        $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
        $qb->join('v.volumeMouzas', 'vm');
        $qb->join('vm.mouza', 'm');
        $qb->where('v.survey = :survey')->setParameter('survey', $serviceRequestPorcha->getSurvey());
        $qb->andWhere('v.district = :district')->setParameter('district', $serviceRequestPorcha->getDistrict());
        $qb->andWhere('v.upozila = :upozila')->setParameter('upozila', $serviceRequestPorcha->getUpozila());
        $qb->andWhere('vm.mouza = :mouza')->setParameter('mouza', $serviceRequestPorcha->getMouza());
        $qb->andWhere('v.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('vm.startKhatianNo <= :khatianNo')->setParameter('khatianNo', $khatianNo);
        $qb->andWhere('vm.endKhatianNo >= :khatianNo')->setParameter('khatianNo', $khatianNo);
        $rows = $qb->getQuery()->getResult();

        if ($rows && count($rows) == 1) {

            if (!$rows[0]->isApproved()) {
                return array('status' => 'UNAPPROVED_VOLUME', 'volumes' => $rows);
            }

            $officeTemplate = $this->hasSurveyTemplate($this->office, $serviceRequestPorcha->getSurvey()->getType());
            if (!$officeTemplate) {
                return array('status' => 'NO_TEMPLATE', 'volumes' => array());
            }

            if (!$this->workflowManager->isPageViewableByWorkflowTeams($rows[0], $serviceRequestPorcha->getMouza(), 'APP')) {
                return array('status' => 'ENTRY_RESTRICTED', 'volumes' => array());
            }

            $khatianLog = $this->newKhatian($rows[0], $serviceRequestPorcha->getMouza(), $officeTemplate[0], 'service');
            return array('status' => 'SUCCESS', 'khatianLog' => $khatianLog);
        }

        return array('status' => 'NO_VOLUME', 'volumes' => $rows);
    }

    public function hasSurveyTemplate(Office $office, $surveyType) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:OfficeTemplate')->createQueryBuilder('ot');
        $qb->join('ot.template', 't');
        $qb->join('t.survey', 's');
        $qb->where('ot.office = :office')->setParameter('office', $office);
        $qb->andWhere('s.type = :surveyType')->setParameter('surveyType', strtoupper($surveyType));
        $qb->andWhere("t.type = 'PAGE1'");
        $qb->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }

    public function newKhatian($volume, $mouza, $officeTemplate, $type) {

        $khatian = new Khatian();
        $khatian->setKhatianNo('');
        $khatian->setVolume($volume);
        $khatian->setOffice($this->office);
        $khatian->setStatus('NONE');
        $khatian->setCorrectionCycle(0);
        $khatian->setMouza($mouza);

        $jlnumber = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
            'mouza' => $mouza,
            'surveyType' => strtoupper($volume->getSurvey()->getType())
        ));

        $khatian->setJlnumber($jlnumber);

        $this->em->persist($khatian);

        $khatianVersion = new KhatianVersion();
        $khatianVersion->setKhatian($khatian);
        $khatianVersion->setVersionNo(($khatianVersion) ? $khatianVersion->getVersionNo() + 1 : 1);
        $khatianVersion->setIdentifier(IdGenerator::getID());
        $this->em->persist($khatianVersion);
        $this->em->flush();

        $khatian->setLastVersion($khatianVersion);
        $this->em->persist($khatian);

        $khatianLog = new KhatianLog();
        $khatianLog->setKhatianVersion($khatianVersion);
        $khatianLog->setEntryOperator($this->user);
        $khatianLog->setEntryAt(new \DateTime());
        $batch = (strtoupper($type) == 'SERVICE') ? false : true;
        $khatianLog->setBatch($batch);
        $khatianLog->setFirstApp(!$batch);
        $khatianLog->setKhatianStatus('NONE');
        $this->em->persist($khatianLog);

        $khatianPage = new KhatianPage();
        $khatianPage->setKhatianVersion($khatianVersion);
        $khatianPage->setType('PAGE1');
        $khatianPage->setOfficeTemplate($officeTemplate);
        $this->em->persist($khatianPage);

        if (!$khatianPage->getCorrectionLog()) {
            $correctionLog = new KhatianCorrectionLog($khatianPage, null);
            $this->em->persist($correctionLog);
        }

        $this->em->flush();

        return $khatianLog;
    }

    public function batchKhatianSearch($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->join('kl.khatianVersion', 'kv');
        $qb->join('kv.khatian', 'k');
        $qb->join('k.volume', 'v');
        $qb->join('v.survey', 's');
        $qb->where('k.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('kl.khatianStatus NOT IN (:status)')->setParameter('status', array_values(array('NONE', 'HAS_ENTRY', 'DRAFT')));
        $qb->andWhere('kl.batch = :batch')->setParameter('batch', true, Type::BOOLEAN);

        $this->filterQuery($qb, $params['arrFilterField']);
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function getKhatianVersions(Khatian $khatian) {

        return $this->em->getRepository('PorchaProcessingBundle:KhatianVersion')->findBy(array('khatian' => $khatian), array('id' => 'asc'));
    }

    private function getEnteredKhatiansByVolume(Volume $volume, $count = false) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->innerJoin('kl.khatianVersion', 'kv');
        $qb->innerJoin('kv.khatian', 'k');
        $qb->innerJoin('k.volume', 'v');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume');
        $qb->setParameter('volume', $volume);
        $qb->andWhere("kl.khatianStatus NOT IN ('NONE')");
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('kl.batch', true),
            $qb->expr()->eq('kl.firstApp', true)
        ));

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }
        $qb->select('kl.id');
        $qb->addSelect('k.khatianNo as khatianNumber');
        $qb->addSelect('k.mouza as mouza');
        return $qb->getQuery()->getResult();
    }

    private function getEnteredKhatiansByMouza(Volume $volume, Mouza $mouza, $count = false) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->innerJoin('kl.khatianVersion', 'kv');
        $qb->innerJoin('kv.khatian', 'k');
        $qb->where('k.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume')->setParameter('volume', $volume);
        $qb->andWhere('k.mouza = :mouza')->setParameter('mouza', $mouza);
        $qb->andWhere("kl.khatianStatus NOT IN ('NONE')");
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('kl.batch', true),
            $qb->expr()->eq('kl.firstApp', true)
        ));

        if ($count) {
            $qb->select('COUNT(k.id)');
            return $qb->getQuery()->getSingleScalarResult();
        }
        $qb->select('kl.id');
        $qb->addSelect('k.khatianNo as khatianNumber');
        return $qb->getQuery()->getResult();
    }

    public function getNoEntryKhatiansByVolume(Volume $volume, $count = false) {

        $arrNoEntryKhatian = array();
        $totalCount = 0;

        foreach ($volume->getVolumeMouzas() as $vm) {

            $arrExists = array();
            $khatians = $this->getEnteredKhatiansByMouza($volume, $vm->getMouza());
            if ($khatians) {
                foreach ($khatians as $khatian) {
                    $arrExists[] = $this->convertNumber('bn2en', $khatian['khatianNumber']);
                }
            }
            $arrAll = range($vm->getEndKhatianNo(), $vm->getStartKhatianNo());
            $arrResult = array_diff($arrAll, $arrExists);
            sort($arrResult);

            if ($count) {
                $totalCount += count($arrResult);
            }

            $arrNoEntryKhatian[$vm->getMouza()->getName()] = $arrResult;
        }

        if ($count) {
            return $totalCount;
        }
        return $arrNoEntryKhatian;
    }

    public function updateCanonicalKhatianNos() {

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $allKhatians = $qb->getQuery()->getResult();

        foreach ($allKhatians as $khatian) {
            $khatian->setCanonicalKhatianNo(str_pad($this->convertNumber('bn2en', $khatian->getKhatianNo()), 5, '0', STR_PAD_LEFT));
        }

        $this->em->flush();
        return true;
    }

    public function updateCanonicalVolumeNos() {

        $allVolumes = $this->em->getRepository('PorchaProcessingBundle:Volume')->findAll();

        $ret = array();
        foreach ($allVolumes as $volume) {
            $volume->setCanonicalvolumeNo(str_pad($volume->getVolumeNo(), 5, '0', STR_PAD_LEFT));
            $ret[] = $volume->getCanonicalvolumeNo();
        }

        $this->em->flush();
        return $ret;
    }

}