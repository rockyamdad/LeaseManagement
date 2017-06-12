<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianCorrectionLog;
use PorchaProcessingBundle\Entity\KhatianVersion;
use Symfony\Component\HttpFoundation\Session\Session;

class KhatianPageCorrectionLogManager
{
    use QueryAssistant;
    protected $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    public function findAllByKhatianPages($khatianPages)
    {
        $qb = $this->em->getRepository('PorchaProcessingBundle:KhatianCorrectionLog')
            ->createQueryBuilder('c')
            ->join('c.khatianPage', 'p')
            ->where('c.khatianPage IN (:page)')
            ->setParameter('page', $khatianPages)
            ->orderBy('p.pageOrder', 'asc')
            ->orderBy('c.id', 'asc')
            ->getQuery()->getResult();

        return $qb;
    }

}