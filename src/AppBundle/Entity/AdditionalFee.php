<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PorchaProcessingBundle\Entity\Survey;

/**
 * AdditionalFee
 *
 * @ORM\Table(name="additional_fees")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdditionalFeeRepository")
 */
class AdditionalFee
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
     * @ORM\Column(name="fee_type_key1", type="string", length=100, nullable=true)
     */
    private $feeTypeKey1;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_type_key2", type="string", length=100, nullable=true)
     */
    private $feeTypeKey2;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_type_key3", type="string", length=100, nullable=true)
     */
    private $feeTypeKey3;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_type_value1", type="decimal", nullable=true)
     */
    private $feeTypeValue1;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_type_value2", type="decimal", nullable=true)
     */
    private $feeTypeValue2;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_type_value3", type="decimal", nullable=true)
     */
    private $feeTypeValue3;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_applicable1", type="json_array", nullable=true)
     */
    private $feeApplicable1;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_applicable2", type="json_array", nullable=true)
     */
    private $feeApplicable2;
    /**
     * @var string
     *
     * @ORM\Column(name="fee_applicable3", type="json_array", nullable=true)
     */
    private $feeApplicable3;
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
    public function getFeeTypeKey1()
    {
        return $this->feeTypeKey1;
    }

    /**
     * @param string $feeTypeKey1
     */
    public function setFeeTypeKey1($feeTypeKey1)
    {
        $this->feeTypeKey1 = $feeTypeKey1;
    }

    /**
     * @return string
     */
    public function getFeeTypeKey2()
    {
        return $this->feeTypeKey2;
    }

    /**
     * @param string $feeTypeKey2
     */
    public function setFeeTypeKey2($feeTypeKey2)
    {
        $this->feeTypeKey2 = $feeTypeKey2;
    }

    /**
     * @return string
     */
    public function getFeeTypeKey3()
    {
        return $this->feeTypeKey3;
    }

    /**
     * @param string $feeTypeKey3
     */
    public function setFeeTypeKey3($feeTypeKey3)
    {
        $this->feeTypeKey3 = $feeTypeKey3;
    }

    /**
     * @return string
     */
    public function getFeeTypeValue1()
    {
        return $this->feeTypeValue1;
    }

    /**
     * @param string $feeTypeValue1
     */
    public function setFeeTypeValue1($feeTypeValue1)
    {
        $this->feeTypeValue1 = $feeTypeValue1;
    }

    /**
     * @return string
     */
    public function getFeeTypeValue2()
    {
        return $this->feeTypeValue2;
    }

    /**
     * @param string $feeTypeValue2
     */
    public function setFeeTypeValue2($feeTypeValue2)
    {
        $this->feeTypeValue2 = $feeTypeValue2;
    }

    /**
     * @return string
     */
    public function getFeeTypeValue3()
    {
        return $this->feeTypeValue3;
    }

    /**
     * @param string $feeTypeValue3
     */
    public function setFeeTypeValue3($feeTypeValue3)
    {
        $this->feeTypeValue3 = $feeTypeValue3;
    }

    /**
     * @return string
     */
    public function getFeeApplicable1()
    {
        return $this->feeApplicable1;
    }

    /**
     * @param string $feeApplicable1
     */
    public function setFeeApplicable1($feeApplicable1)
    {
        $this->feeApplicable1 = $feeApplicable1;
    }

    /**
     * @return string
     */
    public function getFeeApplicable2()
    {
        return $this->feeApplicable2;
    }

    /**
     * @param string $feeApplicable2
     */
    public function setFeeApplicable2($feeApplicable2)
    {
        $this->feeApplicable2 = $feeApplicable2;
    }

    /**
     * @return string
     */
    public function getFeeApplicable3()
    {
        return $this->feeApplicable3;
    }

    /**
     * @param string $feeApplicable3
     */
    public function setFeeApplicable3($feeApplicable3)
    {
        $this->feeApplicable3 = $feeApplicable3;
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