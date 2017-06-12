<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Udc
 *
 * @ORM\Table(name="udc", indexes={
 *     @ORM\Index(name="udc_status_idx", columns={"status"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UdcRepository")
 */
class Udc
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id", nullable=true)
     */
    private $district;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id", nullable=true)
     */
    private $upozila;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Union")
     * @ORM\JoinColumn(name="union_id", referencedColumnName="id", nullable=true)
     */
    private $union;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UdcEntrepreneur", mappedBy="udc", cascade={"persist", "remove"})
     */
    private $udcEntrepreneurs;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="udc")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="udc_name", type="string", nullable=true)
     */
    private $udcName;

    /**
     * @var string
     *
     * @ORM\Column(name="udc_mobile_no", type="string", nullable=true)
     */
    private $udcMobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="udc_address", type="text", nullable=true)
     */
    private $udcAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="udc_email", type="string", nullable=true)
     */
    private $udcEmail;


    /**
     * @var string
     *
     * @ORM\Column(name="us_name", type="string", nullable=true)
     */
    private $usName;

    /**
     * @var string
     *
     * @ORM\Column(name="us_mobile_no", type="string", nullable=true)
     */
    private $usMobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="us_email", type="string", nullable=true)
     */
    private $usEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="us_address", type="text", nullable=true)
     */
    private $usAddress;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="date",nullable=true)
     */
    private $approvedAt;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    private $approvedBy;

    /**
     * @var array $status
     * Values (
    'WAITING_FOR_APPROVAL',
    'APPROVED',
    'CANCELED'
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     */
    public function setOffice($office)
    {
        $this->office = $office;
    }
    /**
     * @return string
     */
    public function getUdcName()
    {
        return $this->udcName;
    }

    /**
     * @param string $udcName
     */
    public function setUdcName($udcName)
    {
        $this->udcName = $udcName;
    }

    /**
     * @return string
     */
    public function getUdcMobileNo()
    {
        return $this->udcMobileNo;
    }

    /**
     * @param string $udcMobileNo
     */
    public function setUdcMobileNo($udcMobileNo)
    {
        $this->udcMobileNo = $udcMobileNo;
    }

    /**
     * @return string
     */
    public function getUdcEmail()
    {
        return $this->udcEmail;
    }

    /**
     * @param string $udcEmail
     */
    public function setUdcEmail($udcEmail)
    {
        $this->udcEmail = $udcEmail;
    }


    /**
     * @return string
     */
    public function getUsName()
    {
        return $this->usName;
    }

    /**
     * @param string $usName
     */
    public function setUsName($usName)
    {
        $this->usName = $usName;
    }

    /**
     * @return string
     */
    public function getUsMobileNo()
    {
        return $this->usMobileNo;
    }

    /**
     * @param string $usMobileNo
     */
    public function setUsMobileNo($usMobileNo)
    {
        $this->usMobileNo = $usMobileNo;
    }

    /**
     * @return string
     */
    public function getUsEmail()
    {
        return $this->usEmail;
    }

    /**
     * @param string $usEmail
     */
    public function setUsEmail($usEmail)
    {
        $this->usEmail = $usEmail;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * @param mixed $upozila
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;
    }

    /**
     * @return mixed
     */
    public function getUnion()
    {
        return $this->union;
    }

    /**
     * @param mixed $union
     */
    public function setUnion($union)
    {
        $this->union = $union;
    }

    /**
     * @return string
     */
    public function getUdcAddress()
    {
        return $this->udcAddress;
    }

    /**
     * @param string $udcAddress
     */
    public function setUdcAddress($udcAddress)
    {
        $this->udcAddress = $udcAddress;
    }

    /**
     * @return string
     */
    public function getUsAddress()
    {
        return $this->usAddress;
    }

    /**
     * @param string $usAddress
     */
    public function setUsAddress($usAddress)
    {
        $this->usAddress = $usAddress;
    }

    /**
     * @return ArrayCollection
     */
    public function getUdcEntrepreneurs()
    {
        return $this->udcEntrepreneurs;
    }

    /**
     * @param ArrayCollection $udcEntrepreneurs
     */
    public function setUdcEntrepreneurs($udcEntrepreneurs)
    {
        $this->udcEntrepreneurs = $udcEntrepreneurs;
    }

    public function __construct()
    {
        $this->udcEntrepreneurs = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


}