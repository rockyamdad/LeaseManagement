<?php

namespace PorchaProcessingBundle\Repository;
use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\Mouza;

/**
 * KhatianRepository
 *
 */
class KhatianRepository extends EntityRepository
{
    public function mappedByField(Khatian $khatian)
    {
        $data = array();
        $data['khatian_no'] = $khatian->getKhatianNo();
        $data['district'] = ($khatian->getJLNumber()) ? $khatian->getJLNumber()->getDistrict() : '';
        $data['thana'] = ($khatian->getJLNumber()) ? $khatian->getJLNumber()->getThana() : '';
        $data['upozila'] = ($khatian->getVolume())? $khatian->getVolume()->getUpozila()->getName() : '';
        $data['mouza'] = ($khatian->getMouza()) ? $khatian->getMouza()->getName() : '';
        $data['jl_no'] = ($khatian->getJLNumber()) ? $khatian->getJLNumber()->getName() : '';
        $data['rs_no'] = $khatian->getRsNo();
        $data['pargana'] = $khatian->getPargana();
        $data['taugi_no'] = $khatian->getTaugiNo();

        return $data;
    }

    public function getKhatianStatisticsByStatus()
    {
        $qb = $this->createQueryBuilder('khatian');
        $qb->select('khatian.status, COUNT(khatian.id) AS total');
        $qb->groupBy('khatian.status');

        $data = array();
        foreach ($qb->getQuery()->getResult() as $row) {
            $data[$row['status']] = $row['total'];
        }

        return $data;
    }
    public function getKhatian($data){

        $qb = $this->getEntityManager()->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->join('k.volume', 'v');
        $qb->join('v.survey', 's');
        $qb->where('k.mouza = :mouza');
        $qb->andWhere('s.id = :sid');
        $qb->andWhere('k.archived = :archived');
        if($data['porchaNo']){
            $qb->andWhere('k.khatianNo = :khatianNo');
            $qb->setParameter('khatianNo', $data['porchaNo']);
        }
        $qb->setParameter('archived', true);
        $qb->setParameter('sid', $data['survey']);
        $qb->setParameter('mouza', $data['mouza']);
        return $qb->getQuery()->getResult();
    }
    public function getVolumes($mouza,$type)
    {
        $query= $this->createQueryBuilder('khat');

        $query->join('khat.volume','vol');
        $query->join('vol.survey','sur');
        $query->where('sur.type = :type');
        $query->andwhere('khat.archived = :status');
        $query->setParameter('type', $type);
        $query->setParameter('status', true);
        $query->andWhere('khat.mouza = :mouza');
        $query->setParameter('mouza', $mouza);
        $data = array();
        $result = $query->getQuery()->getResult();
        $i =0;
        foreach($result as $row){
            $data[$row->getVolume()->getVolumeNo()][$i]['KhatId'] = $row->getId();
            $data[$row->getVolume()->getVolumeNo()][$i]['KhatNo'] = $row->getKhatianNo();
            $data[$row->getVolume()->getVolumeNo()][$i]['volId'] = $row->getVolume()->getId();
            $i++;
        }
        return $data;

    }
    public function findKhatianIdByKhatianNo($param){

        $qb = $this->createQueryBuilder('k');
        $qb->join('k.volume', 'v');
        $qb->select('k.id');
        $qb->where('k.khatianNo = :khatianNo');
        $qb->andWhere('v.survey = :survey');
        $qb->andWhere('v.district = :district');
        $qb->andWhere('v.upozila = :upozila');
        $qb->andWhere('k.mouza = :mouza');
        $qb->setParameter('khatianNo', $param->getKhatianNo());
        $qb->setParameter('survey', $param->getSurvey());
        $qb->setParameter('district', $param->getDistrict());
        $qb->setParameter('upozila', $param->getUpozila());
        $qb->setParameter('mouza', $param->getMouza());
        return $qb->getQuery()->getResult();

    }
    public function khatianBymouza(Mouza $mouza,$survey){

        $qb = $this->createQueryBuilder('k');
        $qb->join('k.mouza', 'm');
        $qb->join('k.jlnumber', 'j');
        $qb->select('k.id,k.khatianNo');
        $qb->where('k.mouza = :mouza');
        $qb->andWhere('j.surveyType = :survey');
        $qb->setParameter('mouza', $mouza);
        $qb->setParameter('survey', $survey);

        return $qb->getQuery()->getResult();
    }

    public function getSaRsKhatianBymouza(Mouza $mouza){

        $qb = $this->createQueryBuilder('k');
        $qb->join('k.mouza', 'm');
        $qb->join('k.jlnumber', 'j');
        $qb->select('k.id,k.khatianNo, j.surveyType');
        $qb->where('k.mouza = :mouza');
        $qb->andWhere($qb->expr()->in('j.surveyType', ':survey'));
        $qb->setParameter('mouza', $mouza);
        $qb->setParameter('survey', array('SA', 'RS'));

        return $qb->getQuery()->getResult();
    }
} 