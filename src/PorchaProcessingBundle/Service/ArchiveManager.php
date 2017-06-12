<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\QueryAssistant;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Generator\IdGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Component\HttpFoundation\Session\Session;

class ArchiveManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }

    public function getBrowseList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
        $qb->leftJoin('v.volumeMouzas', 'vm');
        $qb->where('v.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('v.archived = 1');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

    public function getSearchKhatianList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->innerJoin('kl.khatianVersion', 'lv');
        $qb->innerJoin('lv.khatian', 'k');
        $qb->innerJoin('k.volume', 'v');
        $qb->innerJoin('k.jlnumber', 'j');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere("k.archived = '1'");

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function getArchivedKhatianListByVolume($volume, $data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->innerJoin('k.volume', 'v');
        $qb->innerJoin('k.lastVersion', 'l');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume');
        $qb->setParameter('volume', $volume);
        //$qb->andWhere("k.archived = '1'");

        if (isset($data['isDeliverable'])) {
            if ($data['isDeliverable'] == '1') {
                $qb->andWhere($qb->expr()->isNull('l.nonDeliverableMessage'));
            } else if ($data['isDeliverable'] == '0'){
                $qb->andWhere($qb->expr()->isNotNull('l.nonDeliverableMessage'));
            }
        }

        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getVolumeKhatianCountByDifferentStatus(Volume $volume) {

        $status = array(
            'ARCHIVED' => 0,
            'NON_DELIVERABLE' => 0,
            'ENTRY_RESTRICTED' => 0
        );

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->select('COUNT(k.status) AS archive_count');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume');
        $qb->setParameter('volume', $volume);
        $qb->andWhere("k.archived = '1'");
        $row = $qb->getQuery()->getSingleScalarResult();

        if ($row) {
            $status['ARCHIVED'] = $row;
        }

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->innerJoin('k.lastVersion', 'l');
        $qb->select('COUNT(l.nonDeliverableMessage) AS non_deliverable_count');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume');
        $qb->setParameter('volume', $volume);
        $qb->andWhere("k.archived = '1'");
        $qb->andWhere($qb->expr()->isNotNull('l.nonDeliverableMessage'));
        $row1 = $qb->getQuery()->getSingleScalarResult();

        if ($row1) {
            $status['NON_DELIVERABLE'] = $row1;
        }

        return $status;
    }

    public function khatianSearch($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->select('k');
        $qb->addSelect('m');
        $qb->addSelect('v');
        $qb->addSelect('s');
        $qb->join('k.lastVersion', 'kv');
        $qb->join('PorchaProcessingBundle:KhatianPage', 'kp', 'WITH', 'kp.khatianVersion = kv');
        $qb->join('k.volume', 'v');
        $qb->join('k.mouza', 'm');
        $qb->join('v.survey', 's');
        $qb->where('k.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('k.archived = :archived')->setParameter('archived', true, Type::BOOLEAN);
        $qb->distinct(true);


        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $qb = $this->rangeFilterQuery($qb, $params['arrRangeFilterField1'], $params['arrRangeFilterField2']);

        if (!empty($data['sf']) && !empty($data['sv'])) {

            switch ($data['sf']) {
                case 'otsDokholkar':
                    $qb->andWhere($qb->expr()->orX(
                        $qb->expr()->like('kp.otroSotterBiboronODokholkar', ':otsDokholkar'), $qb->expr()->like('kp.otroSotterBiboronODokholkar2', ':otsDokholkar')
                    ))->setParameter('otsDokholkar', '%'.$data['sv'].'%');
                    break;
                case 'upsDokholkar':
                    $qb->andWhere($qb->expr()->like('kp.uparisthoSotterBiboronDakholkarSangkhipto', ':upsDokholkar'))->setParameter('upsDokholkar', '%'.$data['sv'].'%');
                    break;

            }
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('s.name', 'asc');
            $qb->addOrderBy('m.name', 'asc');
            $qb->addOrderBy('v.canonicalvolumeNo', 'asc');
        }

        return $qb->getQuery()->getResult();
    }

    public function archivedKhatinMultiAction($multiAction, $data, $entryOperator) {

        if (count($data) < 1) {
            return;
        }

        if (in_array($multiAction, array('VIEWABLE', 'NON_VIEWABLE'))) {
            $this->setKhatiansNonDeliverable($data, $multiAction);
        }

        if ($multiAction == 'CORRECTION_REQUIRED') {
            $this->sendKhatiansForRecorrection($data, $entryOperator);
        }

        $this->em->flush();
    }

    private function setKhatiansNonDeliverable($data, $action) {

        foreach ($data as $key=>$val) {

            $khatian = $this->em->getRepository('PorchaProcessingBundle:Khatian')->find($key);

            if ($khatian->getOffice() != $this->office) {
                continue;
            }

            $khatian->setDisplayRestricted(($action == 'NON_VIEWABLE' ? true : false));
            $this->em->persist($khatian);
        }
    }

    private function sendKhatiansForRecorrection($data, $entryOperator) {

        foreach ($data as $key=>$val) {

            $khatian = $this->em->getRepository('PorchaProcessingBundle:Khatian')->find($key);

            if ($khatian->getOffice() != $this->office) {
                continue;
            }

            if ($khatian->isReCorrection()) {
                continue;
            }

            $khatianLastVersion = $khatian->getLastVersion();
            $khatianPages = $this->em->getRepository('PorchaProcessingBundle:KhatianPage')->findBy(array('khatianVersion' => $khatianLastVersion));

            $newKhatianVersion = clone $khatianLastVersion;
            $newKhatianVersion->setId(null);
            $newKhatianVersion->setNewVersionRequired(false);
            $newKhatianVersion->setIdentifier(IdGenerator::getID());
            $this->em->persist($newKhatianVersion);

            foreach ($khatianPages as $page) {

                $newPage = clone $page;
                $newPage->setId(null);
                $newPage->setKhatianVersion($newKhatianVersion);
                $this->em->persist($newPage);
            }

            $entryOperator = $this->em->getRepository('RbsUserBundle:User')->find($entryOperator);

            $newKhatianLog = new KhatianLog();
            $newKhatianLog->setBatch(true);
            $newKhatianLog->setKhatianStatus('CORRECTION_REQUIRED');
            $newKhatianLog->setKhatianVersion($newKhatianVersion);
            $newKhatianLog->setEntryOperator($entryOperator);
            $this->em->persist($newKhatianLog);

            $khatian->setLastVersion($newKhatianVersion);
            $khatian->setReCorrection(1);
            $khatian->setReCorrectionAt(new \DateTime());
            $this->em->persist($khatian);
        }

        $this->em->flush();
    }

    public function updateKhatian(Khatian $khatian, $requestAll) {

        $khatianVersion = $khatian->getLastVersion();

        if (!empty($requestAll['non-deliverable'])) {
            $khatianVersion->setNonDeliverable($requestAll['remark-non-deliverable']);
        } else {
            $khatianVersion->setNonDeliverable(null);
        }

        $khatianVersion->setEditedAt(new \DateTime());
        $khatian->setMouzaMapReference($requestAll['khatian_page']['mouzaMapReference']);

        $this->em->persist($khatian);
        $this->em->persist($khatianVersion);
        $this->em->flush();
    }

    public function getArchivedKhatianVersion($identifier) {

        $khatianVersion = $this->em->getRepository('PorchaProcessingBundle:KhatianVersion')->findOneBy(array('identifier' => $identifier));
        return $khatianVersion->getKhatian();
    }

    public function entryOperatorsForKhatianReCorrection() {

        $qb = $this->em->getRepository('RbsUserBundle:User')->createQueryBuilder('u');
        $qb->join('u.profile', 'p');
        $qb->join('u.groups', 'g');
        $qb->where('u.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('g.roles LIKE :role')->setParameter('role', '%ROLE_KHATIAN_ENTRY%');
        $qb->andWhere('u.enabled = :enabled')->setParameter('enabled', true);
        return $qb->getQuery()->getResult();
    }

}