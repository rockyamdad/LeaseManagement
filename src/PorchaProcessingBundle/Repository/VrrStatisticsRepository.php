<?php

namespace PorchaProcessingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;


class VrrStatisticsRepository extends EntityRepository
{
    public function getStatistics(){
        $qb = $this->createQueryBuilder('v');
        $qb->select('v.totalAppReceived,v.totalAppDelivered,v.totalDigitizedKhatian,v.totalRecordRoom');
        $qb->orderBy('v.id', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }
    public function lastStatistics(){
        $qb = $this->createQueryBuilder('v');
        $qb->orderBy('v.id', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }
}
