<?php

namespace LeaseBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LeaseBundle\Entity\Applicant;
use LeaseBundle\Entity\Application;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository
{
    public function findWaterBodyApplication() {

        $qb = $this->buildApplicationQuery();
        $qb->andWhere("l.type = 'WaterBody'");

        return $this->getApplicationListNonGadgetByQueryBuilder($qb);
        
    }

    public function findMarketApplication() {

        $qb = $this->buildApplicationQuery();
        $qb->join('l.marketDetail', 'md');
        $qb->join('md.market', 'm');
        $qb ->orderBy('m.marketName','DESC');

        return $this->getApplicationListNonGadgetByQueryBuilder($qb);

    }
    
    public function findWaitingForApprovalApplication($leases) {

        $qb = $this->createQueryBuilder('applications');
        $qb->join('applications.lease', 'l');
        $qb->select('COUNT(applications.id) AS totalApp');
        $qb->addSelect('l.id as leaseId');
        $qb->where($qb->expr()->in('l.id', ':leases'));
        $qb->andWhere("applications.status = 'WAITING_FOR_APPROVAL'");
        $qb->setParameter('leases', $leases);
        $qb->groupBy('applications.lease');
        $qb->orderBy('applications.id', 'DESC');
        
        return $qb->getQuery()->getResult();

    }


    public function findAssignedApplication($type){


        $qb = $this->createQueryBuilder('applications');
        $qb->join('applications.lease', 'l');
        if($type=='Market'){
            $qb->join('l.marketDetail', 'md');
            $qb->join('md.market', 'm');
        }
        $qb->select('applications AS leaselist', 'COUNT(applications.id) AS totalApp');
        $qb->where("l.type =:type");
        $qb->andWhere("l.status ='ACTIVE' OR l.status='CLOSED'");
        $qb->andWhere("applications.status = 'ARCHIVED' OR applications.status = 'APPROVED'");
        $qb->setParameter('type',$type);
        $qb->groupBy('applications.lease');

        return $qb;
    }
    public function findApprovedApplications($type){

        $qb = $this->createQueryBuilder('applications');
        $qb->join('applications.lease', 'l');
        if($type=='Market'){
            $qb->join('l.marketDetail', 'md');
            $qb->join('md.market', 'm');
        }
        $qb->where("l.type =:type");
        $qb->andWhere("applications.status = 'APPROVED' ");
        $qb->setParameter('type',$type);
        if($type=='Market') {
            $qb ->orderBy('m.marketName','DESC');
        }
        else{
            $qb->orderBy('applications.id','DESC');
        }
        return $qb;
    }

    public function findGadgetApplication(){

        $qb = $this->buildApplicationQuery();

        $qb->andWhere("l.type =:type");

        return $this->getApplicationListByQueryBuilder($qb);
    }
    
    public function findAssignedGadgetApplication(){

        $qb = $this->createQueryBuilder('applications');
        $qb->join('applications.lease', 'l');
        $qb->select('applications AS leaselist', 'COUNT(applications.id) AS totalApp');
        $qb->where("l.type = 'Gadget'");
        $qb->andWhere("l.status = 'ACTIVE' OR l.status='CLOSED'");
        $qb->andWhere("applications.status = 'ARCHIVED' OR applications.status = 'APPROVED'");
        $qb->groupBy('applications.lease');

        return $qb;
    }
    
    public function create(Application $data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }
    public function findApplicationOfPortal($lease){

        $qb = $this->createQueryBuilder('applications');
        $qb->where('applications.status != :status');
        $qb->andWhere('applications.lease = :lease');
        $qb->setParameter('lease',$lease);
        $qb->setParameter('status','ARCHIVED');

        return $qb->getQuery()->getResult();
    }
    public function findApplicationForGadget($lease){

        $qb = $this->createQueryBuilder('applications');
        $qb->where("applications.status = 'WAITING_FOR_APPROVAL' OR applications.status = 'APPROVED' OR applications.status = 'PENDING'");
        $qb->andWhere('applications.lease = :lease');
        $qb->setParameter('lease',$lease);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function buildApplicationQuery()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->join('a.lease', 'l');
        $qb->select('a as application');
        $qb->addSelect('COUNT(a.id) AS totalApp');
        $qb->addSelect('l.id AS leaseId');
        $qb->andWhere("l.status ='ACTIVE' OR l.status='CLOSED'");
        $qb->andWhere("a.status = 'WAITING_FOR_APPROVAL' OR a.status = 'PENDING' OR a.status = 'CORRECTION' OR a.status = 'APPROVED'");
        $qb->groupBy('l');
        $qb->orderBy('l.id', 'DESC');
        
        return $qb;
    }
    /**
     * @param $qb
     * @return array
     */
    public function getApplicationListNonGadgetByQueryBuilder(QueryBuilder $qb)
    {

        $rows = $qb->getQuery()->getResult();

        $data = array();
        $leases = array();

        foreach ($rows as $row) {
            $data[$row['leaseId']] = array(
                "lease" => $row['application']->getLease(),
                "status" => $row['application']->getStatus(),
                "waitingForCount" => 0,
                "totalApp" => $row['totalApp']
            );

            $leases[] = $row['leaseId'];
        }

        $applicantCount = $this->findWaitingForApprovalApplication($leases);

        foreach ($applicantCount as $row) {
            $data[$row['leaseId']]['waitingForCount'] = $row['totalApp'];
        }

        return $data;
    }

    /**
     * @param $qb
     * @return array
     */
    public function getApplicationListByQueryBuilder(QueryBuilder $qb)
    {
        $qb->setParameter('type', 'Gadget');

        $rows = $qb->getQuery()->getResult();

        $data = array();
        $leases = array();

        foreach ($rows as $row) {
            $data[$row['leaseId']] = array(
                "lease" => $row['application']->getLease(),
                "status" => $row['application']->getStatus(),
                "waitingForCount" => 0,
                "totalApp" => $row['totalApp']
            );

            $leases[] = $row['leaseId'];
        }

        $applicantCount = $this->findWaitingForApprovalApplication($leases);

        foreach ($applicantCount as $row) {
            $data[$row['leaseId']]['waitingForCount'] = $row['totalApp'];
        }

        return $data;
    }
}
