<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PorchaProcessingBundle\Entity\Survey;

/**
 * DeliveryDaySettings
 *
 * @ORM\Table(name="delivery_day_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeliveryDayRepository")
 */
class DeliveryDaySettings
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
     * @var integer
     *
     * @ORM\Column(name="normal_has_entry", type="smallint", nullable=true)
     */
    private $normalDeliveryHasEntry;
    /**
     * @var integer
     *
     * @ORM\Column(name="normal_not_entry", type="smallint", nullable=true)
     */
    private $normalDeliveryNotEntry;
    /**
     * @var integer
     *
     * @ORM\Column(name="normal_non_deliverable", type="smallint", nullable=true)
     */
    private $normalDeliveryNonDeliverable;
    /**
     * @var integer
     *
     * @ORM\Column(name="emergency_has_entry", type="smallint", nullable=true)
     */
    private $emergencyDeliveryHasEntry;

    /**
     * @var integer
     *
     * @ORM\Column(name="emergency_not_entry", type="smallint", nullable=true)
     */
    private $emergencyDeliveryNotEntry;
    /**
     * @var integer
     *
     * @ORM\Column(name="emergency_non_deliverable", type="smallint", nullable=true)
     */
    private $emergencyDeliveryNonDeliverable;

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
     * @return int
     */
    public function getNormalDeliveryHasEntry()
    {
        return $this->normalDeliveryHasEntry;
    }

    /**
     * @param int $normalDeliveryHasEntry
     */
    public function setNormalDeliveryHasEntry($normalDeliveryHasEntry)
    {
        $this->normalDeliveryHasEntry = $normalDeliveryHasEntry;
    }

    /**
     * @return int
     */
    public function getNormalDeliveryNotEntry()
    {
        return $this->normalDeliveryNotEntry;
    }

    /**
     * @param int $normalDeliveryNotEntry
     */
    public function setNormalDeliveryNotEntry($normalDeliveryNotEntry)
    {
        $this->normalDeliveryNotEntry = $normalDeliveryNotEntry;
    }

    /**
     * @return int
     */
    public function getNormalDeliveryNonDeliverable()
    {
        return $this->normalDeliveryNonDeliverable;
    }

    /**
     * @param int $normalDeliveryNonDeliverable
     */
    public function setNormalDeliveryNonDeliverable($normalDeliveryNonDeliverable)
    {
        $this->normalDeliveryNonDeliverable = $normalDeliveryNonDeliverable;
    }

    /**
     * @return int
     */
    public function getEmergencyDeliveryHasEntry()
    {
        return $this->emergencyDeliveryHasEntry;
    }

    /**
     * @param int $emergencyDeliveryHasEntry
     */
    public function setEmergencyDeliveryHasEntry($emergencyDeliveryHasEntry)
    {
        $this->emergencyDeliveryHasEntry = $emergencyDeliveryHasEntry;
    }

    /**
     * @return int
     */
    public function getEmergencyDeliveryNotEntry()
    {
        return $this->emergencyDeliveryNotEntry;
    }

    /**
     * @param int $emergencyDeliveryNotEntry
     */
    public function setEmergencyDeliveryNotEntry($emergencyDeliveryNotEntry)
    {
        $this->emergencyDeliveryNotEntry = $emergencyDeliveryNotEntry;
    }

    /**
     * @return int
     */
    public function getEmergencyDeliveryNonDeliverable()
    {
        return $this->emergencyDeliveryNonDeliverable;
    }

    /**
     * @param int $emergencyDeliveryNonDeliverable
     */
    public function setEmergencyDeliveryNonDeliverable($emergencyDeliveryNonDeliverable)
    {
        $this->emergencyDeliveryNonDeliverable = $emergencyDeliveryNonDeliverable;
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