<?php

namespace PorchaProcessingBundle\Repository;
use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\KhatianVersion;

/**
 * KhatianVersionRepository
 *
 */
class KhatianVersionRepository extends EntityRepository
{
    public function recordCountByDate($date, $office)
    {
        $entry = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.entryAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $entryCorrection = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.entryAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();


        $verify = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.verifiedAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $verifyCorrection = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.verifiedAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $compare = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.comparedAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $compareCorrection = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.comparedAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $approved = $this->createQueryBuilder('kv')
            ->select('COUNT(k.id)')->join('kv.khatian', 'k')
            ->where('kv.approvedAt BETWEEN :date1 AND :date2')->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        return array(
            'ENTRY' => array(
                'complete'   => $entry,
                'correction' => $entryCorrection,
            ),
            'VERIFICATION' => array(
                'complete'   => $verify,
                'correction' => $verifyCorrection,
            ),
            'COMPARISON' => array(
                'complete'   => $compare,
                'correction' => $compareCorrection,
            ),
            'ARCHIVE' => array(
                'complete'   => $approved,
                'correction' => 0,
            ),
        );
    }

    public function countByKhatianTypeQuery($user, $compareField, $type, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $dateField = $compareField['dateField'];
        $userField = $compareField['userField'];
        $qb = $this->createQueryBuilder('kv');
        $qb->join('kv.khatian', 'k');
        $qb->join('k.volume', 'v');
        $qb->join('v.survey', 's');
        $qb->distinct();
        $qb->select('COUNT(kv.id) AS found, s.type');
        $qb->where("kv.{$userField} = :user")->setParameter('user', $user->getId());
        $qb->groupBy('s.type');

        if ($startDate) {
            $qb->addSelect("DAY(kv.{$dateField}) as gDay, MONTH(kv.{$dateField}) as gMonth, YEAR(kv.{$dateField}) as gYear");
            $qb->andWhere("kv.{$dateField} >= :startDate")->setParameter('startDate', $startDate->format('Y-m-d 00:00:00'));
            $qb->addGroupBy('gDay, gMonth, gYear');
        }
        if ($endDate) {
            $qb->andWhere("kv.{$dateField} <= :endDate")->setParameter('endDate', $endDate->format('Y-m-d 23:59:59'));
        }

        if ($type !== 'total') {
            $qb->andWhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED');
        }

        return $qb->getQuery()->getResult();
    }

    public function countByKhatianType($user, $compareField, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        return array(
            'complete' => $this->countByKhatianTypeQuery($user, $compareField, 'total', $startDate, $endDate),
            'correction' => $this->countByKhatianTypeQuery($user, $compareField, 'correction', $startDate, $endDate)
        );
    }
} 