<?php

namespace LeaseBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * HistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoryRepository extends EntityRepository
{
    public function save($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }
}