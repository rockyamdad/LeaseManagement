<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * DCOfficeRepository
 *
 */
class DCOfficeRepository extends EntityRepository
{
    public function searchDCPortal($district)
    {
        return $this->findByDistrictBuilder($district);
    }

    /**
     * @param $district
     *
     * @return QueryBuilder
     */
    private function findByDistrictBuilder($district) {

        return $this->createQueryBuilder('d')
                    ->where('d.slug = :slug')
                    ->setParameter('slug', $district)
                    ->getQuery()->getOneOrNullResult();
    }

} 