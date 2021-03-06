<?php

namespace LeaseBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ApplicantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicantRepository extends EntityRepository
{
    public function findAllApplications(){
        $qb = $this->createQueryBuilder('v');
        $qb->select('v.id');
        $qb->orderBy('v.id', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }
    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }
}
