<?php

namespace PorchaProcessingBundle\Repository\Report;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Report\EntryStatistics;


/**
 * EntryStatisticsRepository
 *
 */
class EntryStatisticsRepository extends EntityRepository
{

    public function updateTodayRecord()
    {
        $this->updateForDate(date('Y-m-d'));
    }

    public function updateForDate($date, $removeExist = true)
    {
        $offices = $this->_em->getRepository('AppBundle:Office')->findBy(array('type' => 'DC'));

        foreach ($offices as $office) {
            if ($removeExist) {
                $this->removeExistRecord($date, $office->getId());
            }
            $this->persistNewRecord($this->recordCountByDate($date, $office->getId()), $date, $office->getId());
        }
    }

    private function removeExistRecord($date, $office)
    {
        $existRecord = $this->_em->getRepository('PorchaProcessingBundle:Report\EntryStatistics')
            ->findBy(array('date' => new \DateTime($date), 'office' => $office));

        foreach ($existRecord as $entity) {
            $this->_em->remove($entity);
        }
        $this->_em->flush();
    }

    public function persistNewRecord($data, $date, $officeId)
    {
        $e = new EntryStatistics();

        $e->setEntry($data['ENTRY']['complete']);
        $e->setEntryCorrection($data['ENTRY']['correction']);
        $e->setEntryTotal($data['ENTRY']['total']);
        $e->setEntryTotalCorrection($data['ENTRY']['totalCorrection']);

        $e->setVerify($data['VERIFICATION']['complete']);
        $e->setVerifyCorrection($data['VERIFICATION']['correction']);
        $e->setVerifyTotal($data['VERIFICATION']['total']);
        $e->setVerifyTotalCorrection($data['VERIFICATION']['totalCorrection']);

        $e->setCompare($data['COMPARISON']['complete']);
        $e->setCompareCorrection($data['COMPARISON']['correction']);
        $e->setCompareTotal($data['COMPARISON']['total']);
        $e->setCompareTotalCorrection($data['COMPARISON']['totalCorrection']);

        $e->setApprove($data['APPROVE']['complete']);
        $e->setApproveCorrection($data['APPROVE']['correction']);
        $e->setApproveTotal($data['APPROVE']['total']);
        $e->setApproveTotalCorrection($data['APPROVE']['totalCorrection']);

        $e->setOffice($officeId);
        $e->setDate(new \DateTime($date));

        $this->_em->persist($e);
        $this->_em->flush();
        $this->_em->clear();
    }

    /**
     * @param $date string
     * @param $office
     * @return array
     */
    public function recordCountByDate($date, $office)
    {
        /**
         * entryAt
         * verifiedAt
         * comparedAt
         * approvedAt
         */
        $khatianLogRepo = $this->_em->getRepository('PorchaProcessingBundle:KhatianLog');
        $qb = $khatianLogRepo->createQueryBuilder('kl');
        $entry = $qb
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where($qb->expr()->gt("kl.entryAt", ':date1'))
            ->andWhere("kl.khatianStatus != 'NONE' ")
            ->andWhere($qb->expr()->lt("kl.entryAt", ':date2'))
            ->andwhere('k.office = :office')
            ->setParameter('date1', new \DateTime($date . ' 00:00:00'), Type::DATETIME)
            ->setParameter('date2', new \DateTime($date . ' 23:59:59'), Type::DATETIME)
            ->setParameter('office', $office)
            ->getQuery()
            ->getSingleScalarResult();

        $entryCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.entryAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $entryTotal = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.entryAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $entryTotalCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.entryAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $verify = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->andWhere("kl.verifiedAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $verifyCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.verifiedAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $verifyTotal = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.verifiedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $verifyTotalCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.verifiedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $compare = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.comparedAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $compareCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.comparedAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $compareTotal = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.comparedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $compareTotalCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.comparedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        $approved = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.approvedAt BETWEEN TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:date2, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 00:00:00')->setParameter('date2', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $approvedTotal = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.approvedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->getQuery()->getSingleScalarResult();

        $approvedTotalCorrection = $khatianLogRepo->createQueryBuilder('kl')
            ->select('COUNT(k.id)')->join('kl.khatianVersion', 'kv')->join('kv.khatian', 'k')
            ->where("kl.approvedAt < TO_DATE(:date1, 'YYYY-MM-DD HH24:MI:SS')")->setParameter('date1', $date . ' 23:59:59')
            ->andwhere('k.office = :office')->setParameter('office', $office)
            ->andwhere('k.status = :status')->setParameter('status', 'CORRECTION_REQUIRED')
            ->getQuery()->getSingleScalarResult();

        return array(
            'ENTRY' => array(
                'complete'   => $entry,
                'correction' => $entryCorrection,
                'total' => $entryTotal,
                'totalCorrection' => $entryTotalCorrection,
            ),
            'VERIFICATION' => array(
                'complete'   => $verify,
                'correction' => $verifyCorrection,
                'total' => $verifyTotal,
                'totalCorrection' => $verifyTotalCorrection,
            ),
            'COMPARISON' => array(
                'complete'   => $compare,
                'correction' => $compareCorrection,
                'total' => $compareTotal,
                'totalCorrection' => $compareTotalCorrection,
            ),
            'APPROVE' => array(
                'complete'   => $approved,
                'correction' => 0,
                'total' => $approvedTotal,
                'totalCorrection' => $approvedTotalCorrection,
            ),
        );
    }
} 