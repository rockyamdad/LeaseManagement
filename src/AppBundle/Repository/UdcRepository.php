<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Udc;
use AppBundle\Entity\UdcEntrepreneur;

/**
 * UdcRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UdcRepository extends EntityRepository
{

    public function create($udc)
    {
        /** @var Udc $udc */
        /** @var UdcEntrepreneur $entrepreneur */

        foreach ($udc->getUdcEntrepreneurs() as $entrepreneur) {
            $entrepreneur->setUdc($udc);
        }
        $this->_em->persist($udc);

        $this->_em->flush();
        return $udc;
    }

}
