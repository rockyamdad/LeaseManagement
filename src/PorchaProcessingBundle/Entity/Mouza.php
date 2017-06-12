<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Mouza
 *
 * @ORM\Table(name="mouzas")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\MouzaRepository")
 */
class Mouza
{
    use ORMBehaviors\Blameable\Blameable;

    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\WorkflowTeam", mappedBy="mouzas", cascade={"persist", "remove"})
     **/
    protected $workflowTeams;

    /**
     * @ORM\OneToMany(targetEntity="PorchaProcessingBundle\Entity\VolumeMouzas", mappedBy="mouza")
     */
    private $volumeMouzas;
    /**
     * @ORM\OneToMany(targetEntity="PorchaProcessingBundle\Entity\JLNumber", mappedBy="mouza")
     */
    private $jlnumbers;
    /**
     * @var string
     *
     * @ORM\Column(name="bbs_geocode", type="string", length=2, nullable=true)
     */
    private $bbsGeocode;
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
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;
    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id")
     */
    private $upozila;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Thana")
     * @ORM\JoinColumn(name="thana_id", referencedColumnName="id")
     */
    private $thana;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;
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
     * @ORM\Column(name="total_service_delivered", type="integer", nullable=true)
     */
    private $totalServiceDelivered;

    public function __toString()
    {
        return $this->getName();
    }

    public function __construct()
    {
        $this->setDeleted(false);
        $this->setApproved(false);
        $this->jlnumbers = new ArrayCollection();
        $this->workflowTeams = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getBbsGeocode()
    {
        return $this->bbsGeocode;
    }

    /**
     * @param string $bbsGeocode
     */
    public function setBbsGeocode($bbsGeocode)
    {
        $this->bbsGeocode = $bbsGeocode;
    }

    /**
     * @return mixed
     */
    public function getJlnumbers()
    {
        return $this->jlnumbers;
    }

    /**
     * @param mixed $jlnumbers
     */
    public function setJlnumbers($jlnumbers)
    {
        $this->jlnumbers = $jlnumbers;
    }

    /**
     * @return mixed
     */
    public function getVolumeMouzas()
    {
        return $this->volumeMouzas;
    }

    /**
     * @param mixed $volumeMouzas
     */
    public function setVolumeMouzas($volumeMouzas)
    {
        $this->volumeMouzas = $volumeMouzas;
    }

    /**
     * @return mixed
     */
    public function getThana()
    {
        return $this->thana;
    }

    /**
     * @param mixed $thana
     */
    public function setThana($thana)
    {
        $this->thana = $thana;
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
     * @return string
     */
    public function getJLNumber()
    {
        return $this->jLNumber;
    }

    /**
     * @param string $jLNumber
     */
    public function setJLNumber($jLNumber)
    {
        $this->jLNumber = $jLNumber;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
