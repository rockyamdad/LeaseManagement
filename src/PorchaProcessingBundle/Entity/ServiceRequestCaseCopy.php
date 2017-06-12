<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceRequestPorcha
 *
 * @ORM\Table(name="service_request_case_copy")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ServiceRequest\ServiceRequestCaseCopyRepository")
 */
class ServiceRequestCaseCopy
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
     * @ORM\OneToOne(targetEntity="PorchaProcessingBundle\Entity\ServiceRequest", cascade={"persist"})
     * @ORM\JoinColumn(name="service_request_id", referencedColumnName="id")
     */
    private $serviceRequest;

    /**
     * @var string
     * @ORM\Column(name="plaintiff_defendant", type="text")
     */
    private $plaintiffDefendant;

    /**
     * @var string
     * @ORM\Column(name="lawyer_name", type="string", length=255, nullable=true)
     */
    private $lawyerName;

    /**
     * @var string
     * @ORM\Column(name="case_no", type="string", length=255, nullable=true)
     */
    private $caseNo;

    /**
     * @var string
     * @ORM\Column(name="court_name", type="string", length=255, nullable=true)
     */
    private $courtName;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ServiceRequest
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
     * @return string
     */
    public function getPlaintiffDefendant()
    {
        return $this->plaintiffDefendant;
    }

    /**
     * @param string $plaintiffDefendant
     *
     * @return ServiceRequestCaseCopy
     */
    public function setPlaintiffDefendant($plaintiffDefendant)
    {
        $this->plaintiffDefendant = $plaintiffDefendant;

        return $this;
    }

    /**
     * @return string
     */
    public function getLawyerName()
    {
        return $this->lawyerName;
    }

    /**
     * @param string $lawyerName
     *
     * @return ServiceRequestCaseCopy
     */
    public function setLawyerName($lawyerName)
    {
        $this->lawyerName = $lawyerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaseNo()
    {
        return $this->caseNo;
    }

    /**
     * @param string $caseNo
     *
     * @return ServiceRequestCaseCopy
     */
    public function setCaseNo($caseNo)
    {
        $this->caseNo = $caseNo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCourtName()
    {
        return $this->courtName;
    }

    /**
     * @param string $courtName
     *
     * @return ServiceRequestCaseCopy
     */
    public function setCourtName($courtName)
    {
        $this->courtName = $courtName;

        return $this;
    }
}