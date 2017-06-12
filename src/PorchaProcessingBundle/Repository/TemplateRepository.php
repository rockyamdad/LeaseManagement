<?php

namespace PorchaProcessingBundle\Repository;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityRepository;

/**
 * TemplateRepository
 *
 */
class TemplateRepository extends EntityRepository
{
    use QueryAssistant;

    public function getTemplateList($data)
    {


        $params = $this->queryParameters($data);

        $qb = $this->createQueryBuilder('t');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

} 