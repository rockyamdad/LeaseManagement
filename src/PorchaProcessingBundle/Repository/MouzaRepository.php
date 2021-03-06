<?php

namespace PorchaProcessingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;

/**
 * MouzaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MouzaRepository extends EntityRepository
{
    public function getTotalKhatianForMouza($mouza,$surveyType){

        $qb = $this->getEntityManager()->getRepository('PorchaProcessingBundle:Khatian')->createQueryBuilder('k');
        $qb->join('k.volume', 'v');
        $qb->join('v.survey', 's');
        $qb->select('count(k.id)');
        $qb->where('k.mouza = :mouza');
        $qb->andWhere('s.type = :type');
        $qb->andWhere('k.archived = 1');
        $qb->setParameter('type', $surveyType);
        $qb->setParameter('mouza', $mouza);
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

}
