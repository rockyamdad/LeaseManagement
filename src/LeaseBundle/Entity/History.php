<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * History
 *
 * @ORM\Table(name="histories")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\LeaseRepository")
 */
class History
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Lease", inversedBy="histories")
     * @ORM\JoinColumn(name="lease_id",referencedColumnName="id")
     */
    private $leaseId;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\Application", inversedBy="history")
     * @ORM\JoinColumn(name="application_id",referencedColumnName="id")
     */
    private $applicationId;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="date",nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="date",nullable=true)
     */
    private $endDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getLeaseId()
    {
        return $this->leaseId;
    }

    /**
     * @param int $leaseId
     */
    public function setLeaseId($leaseId)
    {
        $this->leaseId = $leaseId;
    }

    /**
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param int $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


}
