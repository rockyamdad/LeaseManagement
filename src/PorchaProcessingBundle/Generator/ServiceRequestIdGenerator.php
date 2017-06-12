<?php

namespace PorchaProcessingBundle\Generator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class ServiceRequestIdGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        $qb = $em->getRepository('PorchaProcessingBundle:ServiceRequest')->createQueryBuilder('s');
        $qb->select('COUNT(s.id)');
        $qb->where('YEAR(s.createdAt) = :year')->setParameter('year', date('Y'));
        $qb->andWhere('MONTH(s.createdAt) = :month')->setParameter('month', date('m'));
        $qb->andWhere('DAY(s.createdAt) = :day')->setParameter('day', date('d'));
        $count = (int) $qb->getQuery()->getSingleScalarResult();
        $count++;

        $districtGeocode = $entity->getOffice()->getDistrict()->getGeocode();
        $padded = str_pad($count, 5, '0', STR_PAD_LEFT);

        return (int) $districtGeocode . date('Ymd') . $padded;
    }
}