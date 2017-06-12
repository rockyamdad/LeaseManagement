<?php

namespace PorchaProcessingBundle\Repository;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityRepository;

/**
 * TemplateRepository
 *
 */
class OfficeTemplateRepository extends EntityRepository
{
    use QueryAssistant;

    public function getOfficeTemplateList($data, $geocode) {

        $params = $this->queryParameters($data);

        $qb = $this->createQueryBuilder('ot');
        $qb->innerJoin('ot.template', 't');
        $qb->innerJoin('ot.office', 'o');
        $qb->innerJoin('o.district', 'd');
        $qb->innerJoin('d.division', 'dv');
        $qb->where('d.geocode = :geocode');
        $qb->setParameter('geocode', $geocode);

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }

        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }
} 