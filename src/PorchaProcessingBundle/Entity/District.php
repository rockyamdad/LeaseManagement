<?php

namespace PorchaProcessingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * District
 *
 * @ORM\Table(name="khatian_districts")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\DistrictRepository")
 */
class District
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Division")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id", nullable=true)
     */
    private $division;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="geocode", type="string", length=2)
     */
    private $geocode;
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=true)
     */
    private $approved;
    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_upozila", type="integer", nullable=true)
     */
    private $totalUpozila;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_mouza", type="integer", nullable=true)
     */
    private $totalMouza;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_khatian_cs", type="integer", nullable=true)
     */
    private $totalKhatianCS;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_khatian_rs", type="integer", nullable=true)
     */
    private $totalKhatianRS;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_khatian_sa", type="integer", nullable=true)
     */
    private $totalKhatianSA;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_service_delivered", type="integer", nullable=true)
     */
    private $totalServiceDelivered;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_application_received", type="integer", nullable=true)
     */
    private $totalApplicationReceived;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_udc", type="integer", nullable=true)
     */
    private $totalUDC;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_khatian_app_received", type="integer", nullable=true)
     */
    private $totalKhatianAppReceived;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_khatian_app_delivered", type="integer", nullable=true)
     */
    private $totalKhatianAppDelivered;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_others_app_received", type="integer", nullable=true)
     */
    private $totalOthersAppReceived;
    /**
     * @var integer
     *
     * @ORM\Column(name="total_others_app_delivered", type="integer", nullable=true)
     */
    private $totalOthersAppDelivered;

    /**
     * @var integer
     *
     * @ORM\Column(name="udc_service_delivered", type="integer", nullable=true)
     */
    private $udcServiceDelivered;

    public function __construct()
    {
        $this->setDeleted(false);
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param mixed $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

    /**
     * @return int
     */
    public function getTotalUpozila()
    {
        return $this->totalUpozila;
    }

    /**
     * @param int $totalUpozila
     */
    public function setTotalUpozila($totalUpozila)
    {
        $this->totalUpozila = $totalUpozila;
    }

    /**
     * @return int
     */
    public function getTotalMouza()
    {
        return $this->totalMouza;
    }

    /**
     * @param int $totalMouza
     */
    public function setTotalMouza($totalMouza)
    {
        $this->totalMouza = $totalMouza;
    }

    /**
     * @return int
     */
    public function getTotalKhatianCS()
    {
        return $this->totalKhatianCS;
    }

    /**
     * @param int $totalKhatianCS
     */
    public function setTotalKhatianCS($totalKhatianCS)
    {
        $this->totalKhatianCS = $totalKhatianCS;
    }

    /**
     * @return int
     */
    public function getTotalKhatianRS()
    {
        return $this->totalKhatianRS;
    }

    /**
     * @param int $totalKhatianRS
     */
    public function setTotalKhatianRS($totalKhatianRS)
    {
        $this->totalKhatianRS = $totalKhatianRS;
    }

    /**
     * @return int
     */
    public function getTotalKhatianSA()
    {
        return $this->totalKhatianSA;
    }

    /**
     * @param int $totalKhatianSA
     */
    public function setTotalKhatianSA($totalKhatianSA)
    {
        $this->totalKhatianSA = $totalKhatianSA;
    }

    /**
     * @return int
     */
    public function getTotalServiceDelivered()
    {
        return $this->totalServiceDelivered;
    }

    /**
     * @param int $totalServiceDelivered
     */
    public function setTotalServiceDelivered($totalServiceDelivered)
    {
        $this->totalServiceDelivered = $totalServiceDelivered;
    }

    /**
     * @return int
     */
    public function getUdcServiceDelivered()
    {
        return $this->udcServiceDelivered;
    }

    /**
     * @param int $udcServiceDelivered
     */
    public function setUdcServiceDelivered($udcServiceDelivered)
    {
        $this->udcServiceDelivered = $udcServiceDelivered;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getGeocode()
    {
        return $this->geocode;
    }

    /**
     * @param string $geocode
     */
    public function setGeocode($geocode)
    {
        $this->geocode = $geocode;
    }

    /**
     * @return int
     */
    public function getTotalApplicationReceived()
    {
        return $this->totalApplicationReceived;
    }

    /**
     * @param int $totalApplicationReceived
     */
    public function setTotalApplicationReceived($totalApplicationReceived)
    {
        $this->totalApplicationReceived = $totalApplicationReceived;
    }
    /**
     * @return int
     */
    public function getTotalUDC()
    {
        return $this->totalUDC;
    }

    /**
     * @param int $totalUDC
     */
    public function setTotalUDC($totalUDC)
    {
        $this->totalUDC = $totalUDC;
    }

    /**
     * @return int
     */
    public function getTotalKhatianAppDelivered()
    {
        return $this->totalKhatianAppDelivered;
    }

    /**
     * @param int $totalKhatianAppDelivered
     */
    public function setTotalKhatianAppDelivered($totalKhatianAppDelivered)
    {
        $this->totalKhatianAppDelivered = $totalKhatianAppDelivered;
    }

    /**
     * @return int
     */
    public function getTotalKhatianAppReceived()
    {
        return $this->totalKhatianAppReceived;
    }

    /**
     * @param int $totalKhatianAppReceived
     */
    public function setTotalKhatianAppReceived($totalKhatianAppReceived)
    {
        $this->totalKhatianAppReceived = $totalKhatianAppReceived;
    }

    /**
     * @return int
     */
    public function getTotalOthersAppReceived()
    {
        return $this->totalOthersAppReceived;
    }

    /**
     * @param int $totalOthersAppReceived
     */
    public function setTotalOthersAppReceived($totalOthersAppReceived)
    {
        $this->totalOthersAppReceived = $totalOthersAppReceived;
    }

    /**
     * @return int
     */
    public function getTotalOthersAppDelivered()
    {
        return $this->totalOthersAppDelivered;
    }

    /**
     * @param int $totalOthersAppDelivered
     */
    public function setTotalOthersAppDelivered($totalOthersAppDelivered)
    {
        $this->totalOthersAppDelivered = $totalOthersAppDelivered;
    }

}
