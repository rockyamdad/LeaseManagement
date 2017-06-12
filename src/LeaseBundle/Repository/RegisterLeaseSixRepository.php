<?php

namespace LeaseBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RegisterLeaseSixRepository extends EntityRepository
{
    
    public function getAllAssignLeases(){

        $qb = $this->createQueryBuilder('r');
        return $qb
            ->where($qb->expr()->in('r.status', array('WAITING_FOR_APPROVAL','APPROVED','CORRECTION')))
            ->orderBy('r.id','DESC')
            ;
    }
    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }
    public function getChalanAmount($application){

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Application')->createQueryBuilder('a');
        $qb->select('a.totalAmount/3, (a.totalAmount/3)*2');
        $qb->where("a.id = :aid ")->setParameter('aid', $application);

        $result = $qb->getQuery()->getSingleResult();
        return $result;
    }
    public function getChallanAmountForOneyear($application){

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Application')->createQueryBuilder('a');
        $qb->select('a.totalAmount/3');
        $qb->where("a.id = :aid ")->setParameter('aid', $application);

        $result = $qb->getQuery()->getSingleResult();
        return $result;
    }
}