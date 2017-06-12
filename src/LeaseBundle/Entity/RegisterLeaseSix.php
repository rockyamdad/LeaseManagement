<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RegisterLeaseSix
 *
 * @ORM\Table(name="register_lease_six")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\RegisterLeaseSixRepository")
 */
class RegisterLeaseSix
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
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Application",inversedBy="registerSix")
     * @ORM\JoinColumn(name="application_id",referencedColumnName="id")
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\PaymentSchedule", mappedBy="registerSixes", cascade={"persist", "remove"})
     */
    protected $schedule;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="chalanNo", type="string", length=255,nullable=true)
     */
    private $chalanNo;
    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="nothiNo", type="string", length=255,nullable=true)
     */
    private $nothiNo;
     /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="bank", type="string", length=255,nullable=true)
     */
    private $bank;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="branch", type="string", length=255,nullable=true)
     */
    private $branch;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="chalanDate", type="date",nullable=true)
     */
    private $chalanDate;

    /**
     * @var string
     * @ORM\Column(name="chalanAmount", type="string",nullable=true)
     */
    private $chalanAmount;

    /**
     * @var \DateTime
     * @ORM\Column(name="paymentDate", type="date",nullable=true)
     */
    private $paymentDate;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text",nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="status",type="string",length=255,nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDateTime", type="datetime",nullable=true)
     */
    private $createdDateTime;


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
     * Set chalanNo
     *
     * @param string $chalanNo
     * @return RegisterLeaseSix
     */
    public function setChalanNo($chalanNo)
    {
        $this->chalanNo = $chalanNo;

        return $this;
    }

    /**
     * Get chalanNo
     *
     * @return string 
     */
    public function getChalanNo()
    {
        return $this->chalanNo;
    }

    /**
     * Set chalanDate
     *
     * @param \DateTime $chalanDate
     * @return RegisterLeaseSix
     */
    public function setChalanDate($chalanDate)
    {
        $this->chalanDate = $chalanDate;

        return $this;
    }

    /**
     * Get chalanDate
     *
     * @return \DateTime 
     */
    public function getChalanDate()
    {
        return $this->chalanDate;
    }



    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     * @return RegisterLeaseSix
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime 
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     * @return RegisterLeaseSix
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string 
     */
    public function getRemarks()
    {
        return $this->remarks;
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
     * @return string
     */
    public function getNothiNo()
    {
        return $this->nothiNo;
    }

    /**
     * @param string $nothiNo
     */
    public function setNothiNo($nothiNo)
    {
        $this->nothiNo = $nothiNo;
    }

    /**
     * @return string
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param string $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param mixed $applications
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return string
     */
    public function getChalanAmount()
    {
        return $this->chalanAmount;
    }

    /**
     * @param string $chalanAmount
     */
    public function setChalanAmount($chalanAmount)
    {
        $this->chalanAmount = $chalanAmount;
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


}
