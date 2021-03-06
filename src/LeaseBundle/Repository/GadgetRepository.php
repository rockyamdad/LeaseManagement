<?php

namespace LeaseBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use LeaseBundle\Entity\Applicant;
use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Gadget;
use LeaseBundle\Entity\GadgetDetails;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Form\ApplicantDetailsType;

/**
 * GadgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GadgetRepository extends EntityRepository
{
    public function create(Gadget $data) {

        /** @var GadgetDetails $detail */
        foreach ($data->getGadgetDetails() as $detail) {
            $detail->setGadget($data);
        }

        foreach ($data->getLeases() as $lease) {
            $lease->setGadget($data);
            
            foreach ($lease->getApplications() as $application) {
                $application->setLease($lease);
            }
        }

        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function allGadget() {

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Gadget')->createQueryBuilder('g');
        $qb->join('g.leases', 'l');
        $qb->join('l.applications', 'a');
        $qb->join('a.applicant', 's');
        $qb->select('g.id,g.caseFileNo,g.status,l.startDate,l.endDate,g AS gadget,s.name,a.id AS applicationId,l.status AS leaseStatus');
        $qb->where("l.status = 'SUSPEND'");
        $qb->orWhere("l.status = 'CLOSED'");
        $qb->andWhere("a.status = 'APPROVED'");
        $qb->andWhere("g.status = 'APPROVED'");
        $qb->groupBy('g.id');
        $qb->orderBy('g.id','DESC');

        return $qb;
    }

    public function forACLandAllOpenGadget(){

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Gadget')->createQueryBuilder('g');
        $qb->leftJoin('g.lease', 'l');
        $qb->select('g');
        $qb->where('l.status !=:leaseStatus');
        $qb->orWhere('g.lease IS NULL');
        $qb->setParameter('leaseStatus','CLOSED');
        $qb->groupBy('g.id');
        $qb->orderBy('g.id','DESC');
        return $qb;
    }
    public function forUnoAllOpenGadget(){

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Gadget')->createQueryBuilder('g');
        $qb->leftJoin('g.lease', 'l');
        $qb->select('g');
        $qb->where('l.status !=:leaseStatus');
        $qb->orWhere('g.lease IS NULL');
        $qb->andWhere('g.status !=:gadgetStatus');
        $qb->setParameter('leaseStatus','CLOSED');
        $qb->setParameter('gadgetStatus','PENDING');
        $qb->groupBy('g.id');
        $qb->orderBy('g.id','DESC');
        return $qb;
    }

    public function getLeasee($gadget){

        $qb = $this->getEntityManager()->getRepository('LeaseBundle:Gadget')->createQueryBuilder('g');
        $qb->join('g.leases', 'l');
        $qb->join('l.applications', 'a');
        $qb->join('a.applicants', 'applicant');

        $qb->select('applicant.name,applicant.dob,applicant.nid,applicant.phoneNo,applicant.address');
        $qb->where('l.status= :leaseStatus');
        $qb->andWhere('l.gadget= :gadget');
        $qb->andWhere('a.status= :appStatus');
        $qb->setParameter('leaseStatus','Activate');
        $qb->setParameter('gadget',$gadget);
        $qb->setParameter('appStatus','Activate');

        return $qb->getQuery()->getResult();
    }
}
