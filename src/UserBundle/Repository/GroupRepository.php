<?php

namespace UserBundle\Repository;

use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * GroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupRepository extends EntityRepository
{
    use QueryAssistant;

    public function getAll()
    {
        return $this->findAll();
    }

    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function update($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $this->_em;
    }

    public function groups()
    {
        $query = $this->createQueryBuilder('g');

        return $query->getQuery()->getResult();
    }

    public function getGroupList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->createQueryBuilder('g');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }
    
    public function getGroupOfOwnOffice(User $loginUser = null)
    {
        $qb = $this->createQueryBuilder('g')
            ->andWhere("g.name != :group")->setParameter('group', 'Super Administrator');
        if ($loginUser && $loginUser->getOffice()) {
            $qb->andWhere('g.applicableTo = :applicateTo')->setParameter('applicateTo', $loginUser->getOffice()->getType());
        }

        return $qb->getQuery()->getResult();
    }
}
