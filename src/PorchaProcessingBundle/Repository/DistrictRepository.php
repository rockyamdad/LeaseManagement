<?php

namespace PorchaProcessingBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

/**
 * DistrictRepository
 *
 */
class DistrictRepository extends EntityRepository
{
    public function getTotalUpozila($district){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(upozila.id)');
        $qb->where('upozila.district = :district');
        $qb->andWhere('upozila.approved = 1');
        $qb->setParameter('district', $district);

        $qb->from('PorchaProcessingBundle:Upozila','upozila');

        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }
    public function getTotalReceivedApplication($office){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(ServiceRequest.id)');
        $qb->where('ServiceRequest.office = :office');
        $qb->setParameter('office', $office);
        $qb->from('PorchaProcessingBundle:ServiceRequest','ServiceRequest');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    public function getTotalMouzaForDistrict($district){

        $qb = $this->getEntityManager()->getRepository('PorchaProcessingBundle:Mouza')->createQueryBuilder('m');
        $qb->join('m.upozila', 'u');

        $qb->select('count(m.id)');
        $qb->where('u.district = :district');
        $qb->andWhere('m.approved = 1');
        $qb->setParameter('district', $district);
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }
    public function getTotalKhatianForDistrict($district, $surveyType){

        $qb = $this->getEntityManager()->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->join('k.mouza', 'm');
        $qb->join('k.volume', 'v');
        $qb->join('v.survey', 's');

        $qb->select('count(k.id)');
        $qb->where('v.district = :district');
        $qb->andWhere('s.type = :type');
        $qb->andWhere('k.archived = 1');
        $qb->setParameter('type', $surveyType);
        $qb->setParameter('district', $district);
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }
    public function getTotalApplicationReceived(){
        $qb = $this->createQueryBuilder('d');
        $qb->select('SUM(d.totalApplicationReceived) AS total');
        return $qb->getQuery()->getResult();
    }
    public function getTotalRecordDigitalized(){

        $qb = $this->createQueryBuilder('d');
        $qb->select('SUM(d.totalKhatianCS + d.totalKhatianRS + d.totalKhatianSA) AS total');
        $qb->where('d.totalKhatianSA IS NOT NULL');
        $qb->andWhere('d.totalKhatianCS IS NOT NULL');
        $qb->andWhere('d.totalKhatianRS IS NOT NULL');
        return $qb->getQuery()->getResult();

    }
    public function getTotalRecordRoom(){
        $qb = $this->createQueryBuilder('d');
        $qb->select('count(d.id) AS total');
        return $qb->getQuery()->getResult();
    }

} 