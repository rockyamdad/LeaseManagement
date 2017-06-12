<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Application
 *
 * @ORM\Table(name="applications")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\ApplicationRepository")
 */
class Application
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
     * @var string
     *
     * @ORM\Column(name="phoneNo", type="string", length=255,nullable=true)
     */
    private $phoneNo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="application_details", type="text",nullable=true)
     */
    private $applicationDetails;
    /**
     * @var string
     *
     * @ORM\Column(name="application_tracking_id", type="text",nullable=true)
     */
    private $applicationTrackingId;

    /**
     * @var string
     *
     * @ORM\Column(name="otp", type="text",nullable=true)
     */
    private $otp;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Lease", inversedBy="applications")
     * @ORM\JoinColumn(name="lease_id",referencedColumnName="id")
     */
    private $lease;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\Applicant", mappedBy="application", cascade={"persist", "remove"})
     */
    protected $applicant;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\History", mappedBy="applicationId")
     */
    protected $history;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\RegisterLeaseSix", mappedBy="applications", cascade={"persist", "remove"})
     */
    protected $registerSix;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\PaymentSchedule", mappedBy="applications", cascade={"persist", "remove"})
     */
    protected $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDateTime", type="datetime",nullable=true)
     */
    private $createdDateTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approvedDateTime", type="datetime",nullable=true)
     */
    private $approvedDateTime;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount", type="float", length=255,nullable=true)
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255,nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->registerSix = new ArrayCollection();
        $this->createdDateTime = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getLease()
    {
        return $this->lease;
    }

    /**
     * @param mixed $lease
     */
    public function setLease(Lease $lease)
    {
        $this->lease = $lease;
        $this->lease->addApplication($this);
    }
    /**
     * @param mixed $registerSix
     */
    public function addRegisterSix($registerSix)
    {
        if (!$this->registerSix->contains($registerSix)) {
            $this->registerSix->add($registerSix);
        }
    }

    /**
     * @param mixed $registerSix
     */
    public function removeWaterBodyDetail($registerSix)
    {
        if ($this->registerSix->contains($registerSix)) {
            $this->registerSix->removeElement($registerSix);
        }
    }

    /**
     * @return string
     */
    public function getApplicationDetails()
    {
        return $this->applicationDetails;
    }

    /**
     * @param string $applicationDetails
     */
    public function setApplicationDetails($applicationDetails)
    {
        $this->applicationDetails = $applicationDetails;
    }

    /**
     * @return mixed
     */
    public function getRegisterSix()
    {
        return $this->registerSix;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDateTime()
    {
        return $this->createdDateTime;
    }

    /**
     * @param \DateTime $createdDateTime
     */
    public function setCreatedDateTime($createdDateTime)
    {
        $this->createdDateTime = $createdDateTime;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param mixed $registerSix
     */
    public function setRegisterSix($registerSix)
    {
        $this->registerSix = $registerSix;
    }

    /**
     * @return mixed
     */
    public function getApplicationTrackingId()
    {
        return $this->applicationTrackingId;
    }

    /**
     * @param mixed $applicationTrackingId
     */
    public function setApplicationTrackingId($applicationTrackingId)
    {
        $this->applicationTrackingId = $applicationTrackingId;
    }

    /**
     * @return string
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * @param string $phoneNo
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;
    }

    /**
     * @return mixed
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * @param mixed $applicant
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * @return mixed
     */
    public function getOtp()
    {
        return $this->otp;
    }

    /**
     * @param mixed $otp
     */
    public function setOtp($otp)
    {
        $this->otp = $otp;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedDateTime()
    {
        return $this->approvedDateTime;
    }

    /**
     * @param \DateTime $approvedDateTime
     */
    public function setApprovedDateTime($approvedDateTime)
    {
        $this->approvedDateTime = $approvedDateTime;
    }

}
