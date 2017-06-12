<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\EntityAssistant;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\VolumeIndex;
use PorchaProcessingBundle\Entity\VolumeMouzas;
use PorchaProcessingBundle\Entity\VolumeTemplate;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Query\ResultSetMapping;

class VolumeManager
{
    use QueryAssistant;
    use EntityAssistant;

    protected $em;
    protected $user;
    protected $office;
    protected $khatianManager;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, KhatianManager $khatianManager) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
        $this->khatianManager = $khatianManager;
    }

    public function getNewVolumes($volumeId) {
        return $this->em->getRepository('PorchaProcessingBundle:Volume')->findOneBy(array(
            'id' => $volumeId,
            'archived' => 0,
            'office' => $this->office
        ));
    }

    public function getVolumeList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
        $qb->join('v.volumeMouzas', 'vm');
        $qb->join('vm.mouza', 'm');
        $qb->where('v.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('v.deleted = 0');
        $qb->andWhere('v.archived = 0');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('v.survey', 'asc');
            $qb->addOrderBy('m.name', 'asc');
            $qb->addOrderBy('v.canonicalvolumeNo', 'asc');
        }
        $this->filterQuery($qb, $params['arrFilterField']);
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function getKhatianList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->innerJoin('k.volume', 'v');
        $qb->where("k.office = :office");
        $qb->setParameter('office', $this->office);

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

    public function getKhatianListByVolume($volume, $data) {

        $params = $this->queryParameters($data);

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

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('k.canonicalKhatianNo', 'asc');
        }
        $this->filterQuery($qb, $params['arrFilterField']);
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function getVolumeTemplates(Volume $volume) {
        return $this->em->getRepository('PorchaProcessingBundle:VolumeTemplate')->findBy(array('volume' => $volume), array('type' => 'asc'));
    }

    public function getNextTemplateType(Volume $volume) {
        $row = $this->em->getRepository('PorchaProcessingBundle:VolumeTemplate')->findOneBy(array('volume' => $volume), array('type' => 'desc'));
        return ($row) ? $row->getType() + 1 : 1;
    }

    public function approvedKhatianCount(Volume $volume) {

        $qb = $this->em->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->select('COUNT(k.id)');
        $qb->where('k.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume')->setParameter('volume', $volume);
        $qb->andWhere('k.archived = 1');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function deleteVolume(Volume $volume) {

        if ($this->isVolumeDeletable($volume)) {
            $volume->setDeleted(true);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function isVolumeDeletable(Volume $volume) {

        if ($volume->getOffice()->getId() != $this->office->getId()) {
            return false;
        }

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->join('kl.khatianVersion', 'kv');
        $qb->join('kv.khatian', 'k');
        $qb->where('k.office = :office')->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume')->setParameter('volume', $volume);
        $qb->andWhere("kl.khatianStatus != 'NONE' ");
        $qb->select('COUNT(k.id)');

        return ($qb->getQuery()->getSingleScalarResult()) ? false : true;

    }

    public function addVolume(Volume $volume) {

        $volume->setOffice($this->office);

        $createMode = !$volume->getId();
        if ($createMode) {
            $volume->setCreatedAt(new \DateTime());
            $volume->setCreatedBy($this->user);
        }

        $noEntryKhatianCount = 0;
        foreach ($volume->getVolumeMouzas() as $m) {
            /**@var VolumeMouzas $m*/
            $m->setVolume($volume);
            $noEntryKhatianCount += $m->getEndKhatianNo() - $m->getStartKhatianNo() + 1;
            $this->em->persist($m);
        }

        if ($createMode) {
            $volume->setNoEntryKhatianCount($noEntryKhatianCount);
            $volume->setNoOfKhatians($noEntryKhatianCount);
        } else {
            if ($volume->getNoEntryKhatianCount() != $noEntryKhatianCount) {
                $volume->setNoEntryKhatianCount($noEntryKhatianCount);
                $volume->setNoOfKhatians($noEntryKhatianCount);
            }
        }

        $this->em->persist($volume);
        $this->em->flush();
    }

    public function updateVolume(Volume $volume, $exVolumeMouzas) {

        $noEntryKhatianCount = 0;

        $arrExMouza = array();
        if ($exVolumeMouzas) {
            foreach ($exVolumeMouzas as $exVm) {
                $arrExMouza[] = $exVm->getMouza()->getId();
            }
        }

        foreach ($volume->getVolumeMouzas() as $m) {

            if (!in_array($m->getMouza()->getId(), $arrExMouza)) {
                /**@var VolumeMouzas $m*/
                $m->setVolume($volume);
                $noEntryKhatianCount += $m->getEndKhatianNo() - $m->getStartKhatianNo() + 1;
                $this->em->persist($m);
            }
        }

        $volume->setNoOfKhatians($noEntryKhatianCount);
        $volume->setNoEntryKhatianCount($this->khatianManager->getNoEntryKhatiansByVolume($volume, true));
        $this->em->persist($volume);
        $this->em->flush();
    }

    public function approveVolumeOnly($volumeId, $approved) {

        $volume = $this->em->getRepository('PorchaProcessingBundle:Volume')->find($volumeId);
        $volume->setApproved($approved);
        $this->em->persist($volume);
        $this->em->flush();
    }

    public function ifVolumeNoExist($requestAll) {

        $data = $requestAll['porcha_processing_volume'];

        $volumeMouzas = $data['volumeMouzas'];
        foreach ($volumeMouzas as $mouza) {

            $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
            $qb->select('m.name');
            $qb->join('v.volumeMouzas', 'vm');
            $qb->join('vm.mouza', 'm');
            $qb->where('v.office = :office')->setParameter('office', $this->office);
            $qb->andWhere('v.deleted = :deleted')->setParameter('deleted', false);
            $qb->andWhere('v.survey = :survey')->setParameter('survey', $data['survey']);
            $qb->andWhere('v.volumeNo = :volumeNo')->setParameter('volumeNo', $this->convertNumber('en2bn', $data['volumeNo']));
            $qb->andWhere('vm.mouza = :mouza')->setParameter('mouza', $mouza['mouza']);
            $qb->setMaxResults(1);
            $row = $qb->getQuery()->getOneOrNullResult();

            if ($row) {
                return $row['name'];
            }
        }
        return false;
    }

    public function ifKhatianRangeExist($requestAll) {

        $volumeId = $requestAll['volumeId'];
        $data = $requestAll['porcha_processing_volume'];

        $arrMatch = array();
        if ($volumeId) {
            $existingVolume = $this->em->getRepository('PorchaProcessingBundle:Volume')->find($volumeId);
            $data['survey'] = $existingVolume->getSurvey()->getId();
            /**@var VolumeMouzas $existingMouzas*/
            $existingMouzas = $existingVolume->getVolumeMouzas();
            foreach($existingMouzas as $existingMouza) {
                $arrMatch[] = $existingMouza->getId();
            }
        }

        $volumeMouzas = $data['volumeMouzas'];

        foreach ($volumeMouzas as $key => $mouza) {

            $start = $mouza['startKhatianNo'];
            $end = $mouza['endKhatianNo'];

            $except = false;
            if (!empty($arrMatch)) {
                if (isset($mouza['id']) && in_array($mouza['id'], $arrMatch)) {
                    $except = $mouza['id'];
                }
            }

            $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
            $qb->select('vm.startKhatianNo, vm.endKhatianNo, m.name as mouzaName');
            $qb->join('v.volumeMouzas', 'vm');
            $qb->join('vm.mouza', 'm');
            $qb->where('v.office = :office')->setParameter('office', $this->office);
            $qb->andWhere('v.deleted = :deleted')->setParameter('deleted', false);
            $qb->andWhere('v.survey = :survey')->setParameter('survey', $data['survey']);
            $qb->andWhere('vm.mouza = :mouza')->setParameter('mouza', $mouza['mouza']);

            if ($except !== false) {
                $qb->andWhere('vm.id != :except')->setParameter('except', $except);
            }

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->andX('vm.startKhatianNo >= :start', 'vm.startKhatianNo <= :end'),
                $qb->expr()->andX('vm.endKhatianNo >= :start', 'vm.endKhatianNo <= :end'),
                $qb->expr()->andX('vm.startKhatianNo <= :start', 'vm.endKhatianNo >= :end')
            )
            )->setParameter('start', $start)->setParameter('end', $end);
            $qb->setMaxResults(1);
            $row = $qb->getQuery()->getOneOrNullResult();

            if ($row) {
                return array(
                    'startKhatianNo' => $row['startKhatianNo'],
                    'endKhatianNo' => $row['endKhatianNo'],
                    'mouza' => $row['mouzaName']
                );
            }
        }
        return false;
    }

    public function getVolumeKhatianStatusCount(Volume $volume) {

        $khatianStatus = array(
            'READY_FOR_VERIFICATION' => 0,
            'READY_FOR_COMPARISON' => 0,
            'READY_FOR_APPROVAL' => 0,
            'CORRECTION_REQUIRED' => 0,
            'APPROVED' => 0,
            'DRAFT' => 0
        );

        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianLog')->createQueryBuilder('kl');
        $qb->join('kl.khatianVersion', 'kv');
        $qb->join('kv.khatian', 'k');
        $qb->select('kl.khatianStatus AS status, COUNT(kl.khatianStatus) AS status_count');
        $qb->where('k.office = :office');
        $qb->setParameter('office', $this->office);
        $qb->andWhere('k.volume = :volume');
        $qb->setParameter('volume', $volume);
        $qb->andWhere("kl.khatianStatus NOT IN ('NONE')");
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('kl.batch', true),
            $qb->expr()->eq('kl.firstApp', true)
        ));
        $qb->groupBy('kl.khatianStatus');
        $rows = $qb->getQuery()->getResult();

        foreach ($rows as $status) {
            if (in_array($status['status'], $khatianStatus)) {
                $khatianStatus[$status['status']] = $status['status_count'];
            }
        }

        return $khatianStatus;
    }

    public function archiveVolume(Volume $volume) {

        if ($volume->getOffice() != $this->office)  {
            return false;
        }

        $volume->setArchived(true);
        $this->em->flush();

        return true;
    }

    public function getVolumeDatails(Volume $volume) {

        $info = array();
        foreach ($volume->getVolumeMouzas() as $volumeMouza) {

            $row = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
                'mouza' => $volumeMouza->getMouza(),
                'surveyType' => strtoupper($volume->getSurvey()->getType())
            ));

            $info[] = array(
                'survey' => $volume->getSurvey()->getName(),
                'volumeNo' => $volume->getVolumeNo(),
                'district' => ($row) ? $row->getDistrict() : '',
                'upozila' => ($row) ? $row->getThana() : '',
                'mouza' => $volumeMouza->getMouza()->getName(),
                'jlnumber' => ($row) ? $row->getName() : ''
            );
        }
        return $info;
     }

    public function saveVolumeIndexes(Volume $volume) {
        /** @var VolumeIndex $index */
        foreach ($volume->getIndexes() as $index) {
            $index->setVolume($volume);
        }
        $this->em->persist($volume);
        $this->em->flush();
    }

    public function getJlnumberBySurveyType(Mouza $mouza, $surveyType) {

        $ret = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
            'mouza' => $mouza,
            'surveyType' => strtoupper($surveyType)
        ));

        return array(
            'name' => ($ret) ? $ret->getName() : '',
            'district' => ($ret) ? $ret->getDistrict() : '',
            'thana' => ($ret) ? $ret->getThana() : ''
        );
    }

    public function getVolumesByMouza($mouzaId)
    {

        $qb = $this->em->getRepository('PorchaProcessingBundle:Volume')->createQueryBuilder('v');
        $qb->join('v.volumeMouzas','vm');
        $qb->where('v.deleted = 0');
        $qb->andWhere('vm.mouza = :volumeMouzas');
        $qb->setParameter('volumeMouzas',$mouzaId);
        return $qb->getQuery()->getResult();
    }
}