<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Upozila
 *
 * @ORM\Table(name="khatian_upozilas")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\UpozilaRepository")
 */
class Upozila
{
    use ORMBehaviors\Blameable\Blameable;

    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\WorkflowTeam", mappedBy="upozilas", cascade={"persist", "remove"})
     **/
    protected $workflowTeams;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private $district;
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
     * @ORM\Column(name="geocode", type="string", length=20)
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
        $this->workflowTeams = new ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Upozila
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Upozila
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
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
