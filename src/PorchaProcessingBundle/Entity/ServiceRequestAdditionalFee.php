<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Entity\Office;
use AppBundle\Traits\EntityAssistant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ServiceRequest
 *
 * @ORM\Table(name="sr_additional_fees")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ServiceRequest\AdditionalFeeRepository")
 */
class ServiceRequestAdditionalFee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
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
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\ServiceRequest")
     * @ORM\JoinColumn(name="service_request_id", referencedColumnName="id")
     */
    private $serviceRequest;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return ServiceRequest
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     *
     * @return ServiceRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
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
     * @return ServiceRequest
     */
    public function setServiceRequest($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }
    
}