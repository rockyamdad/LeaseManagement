<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceRequestPorcha
 *
 * @ORM\Table(name="service_request_porcha")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ServiceRequest\ServiceRequestPorchaRepository")
 */
class ServiceRequestPorcha
{
    use EntityAssistant;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianLog")
     * @ORM\JoinColumn(name="khatian_log_id", referencedColumnName="id")
     */
    private $khatianLog;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Survey")
     * @ORM\JoinColumn(name="survery_id", referencedColumnName="id")
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private $district;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id")
     */
    private $upozila;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Mouza")
     * @ORM\JoinColumn(name="mouza_id", referencedColumnName="id")
     */
    private $mouza;

    /**
     * @var string
     *
     * @ORM\Column(name="jl_no", type="string", length=255, nullable=true)
     */
    private $jlNo;

    /**
     * @var string
     *
     * @ORM\Column(name="khatianNo", type="string", length=255, nullable=true)
     */
    private $khatianNo;

    /**
     * @var string
     *
     * @ORM\Column(name="dag_no", type="string", length=255, nullable=true)
     */
    private $dagNo;

    /**
     * @var string
     *
     * @ORM\Column(name="sheet_no", type="string", length=255, nullable=true)
     */
    private $sheetNo;

    /**
     * @var string
     *
     * @ORM\Column(name="correction_reason", type="text", nullable=true)
     */
    private $correctionReason;

    /**
     * @var string
     *
     * @ORM\Column(name="correction_info", type="text", nullable=true)
     */
    private $correctionInfo;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\ServiceRequest")
     * @ORM\JoinColumn(name="service_request_id", referencedColumnName="id")
     */
    private $serviceRequest;


    /**
     * @return KhatianLog
     */
    public function getKhatianLog()
    {
        return $this->khatianLog;
    }

    /**
     * @param mixed $khatianLog
     */
    public function setKhatianLog($khatianLog)
    {
        $this->khatianLog = $khatianLog;
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
     * Get surveyType
     *
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set surveyType
     *
     * @param Survey $surveyType
     * @return ServiceRequestPorcha
     */
    public function setSurvey($surveyType)
    {
        $this->survey = $surveyType;

        return $this;
    }

    /**
     * Get upozila
     *
     * @return Upozila
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * Set upozila
     *
     * @param integer $upozila
     * @return ServiceRequestPorcha
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;

        return $this;
    }

    /**
     * Get mouza
     *
     * @return Mouza
     */
    public function getMouza()
    {
        return $this->mouza;
    }

    /**
     * Set mouza
     *
     * @param integer $mouza
     * @return ServiceRequestPorcha
     */
    public function setMouza($mouza)
    {
        $this->mouza = $mouza;

        return $this;
    }

    /**
     * Get jlNo
     *
     * @return JLNumber
     */
    public function getJlNo()
    {
        return $this->jlNo;
    }

    /**
     * Set jlNo
     *
     * @param string $jlNo
     * @return ServiceRequestPorcha
     */
    public function setJlNo($jlNo)
    {
        $this->jlNo = $jlNo;

        return $this;
    }

    /**
     * Get khatianNo
     *
     * @return string
     */
    public function getKhatianNo()
    {
        return $this->khatianNo;
    }

    /**
     * Set khatianNo
     *
     * @param string $khatianNo
     * @return ServiceRequestPorcha
     */
    public function setKhatianNo($khatianNo)
    {
        $this->khatianNo = $this->convertNumber('en2bn', $khatianNo);

        return $this;
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
     *
     * @return ServiceRequestPorcha
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceRequest()
    {
        return $this->serviceRequest;
    }

    /**
     * @param mixed $serviceRequest
     *
     * @return ServiceRequestPorcha
     */
    public function setServiceRequest($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDagNo()
    {
        return $this->dagNo;
    }

    /**
     * @param mixed $dagNo
     *
     * @return ServiceRequestPorcha
     */
    public function setDagNo($dagNo)
    {
        $this->dagNo = $dagNo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSheetNo()
    {
        return $this->sheetNo;
    }

    /**
     * @param mixed $sheetNo
     *
     * @return ServiceRequestPorcha
     */
    public function setSheetNo($sheetNo)
    {
        $this->sheetNo = $sheetNo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrectionReason()
    {
        return $this->correctionReason;
    }

    /**
     * @param string $correctionReason
     */
    public function setCorrectionReason($correctionReason)
    {
        $this->correctionReason = $correctionReason;
    }

    /**
     * @return string
     */
    public function getCorrectionInfo()
    {
        return $this->correctionInfo;
    }

    /**
     * @param string $correctionInfo
     */
    public function setCorrectionInfo($correctionInfo)
    {
        $this->correctionInfo = $correctionInfo;
    }
}
