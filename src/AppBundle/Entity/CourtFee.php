<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PorchaProcessingBundle\Entity\Survey;

/**
 * CourtFee
 *
 * @ORM\Table(name="court_fees")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourtFeeRepository")
 */
class CourtFee
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=true)
     */
    private $survey;
    /**
     * @var string
     * Values (
    'PORCHA',
    'INFORMATION_SLIP',
    'CASE_COPY',
    'MOUZA_MAP'
     * )
     * @ORM\Column(name="application_type", type="string", length=50, nullable=true)
     */
    private $applicationType;
    /**
     * @var string
     *
     * @ORM\Column(name="normal_court_fee", type="decimal", nullable=true)
     */
    private $normalCourtFee;
    /**
     * @var string
     *
     * @ORM\Column(name="emergency_court_fee", type="decimal", nullable=true)
     */
    private $emergencyCourtFee;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;
    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked = false;

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
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param mixed $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * @param string $applicationType
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    /**
     * @return string
     */
    public function getNormalCourtFee()
    {
        return $this->normalCourtFee;
    }

    /**
     * @param string $normalCourtFee
     */
    public function setNormalCourtFee($normalCourtFee)
    {
        $this->normalCourtFee = $normalCourtFee;
    }

    /**
     * @return string
     */
    public function getEmergencyCourtFee()
    {
        return $this->emergencyCourtFee;
    }

    /**
     * @param string $emergencyCourtFee
     */
    public function setEmergencyCourtFee($emergencyCourtFee)
    {
        $this->emergencyCourtFee = $emergencyCourtFee;
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
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

}