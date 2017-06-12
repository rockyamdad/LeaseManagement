<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * SmsLog
 *
 * @ORM\Table(name="sms_logs")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ThanaRepository")
 */
class SmsLog
{
    use ORMBehaviors\Blameable\Blameable,
        ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\ServiceRequest")
     * @ORM\JoinColumn(name="service_request_id", referencedColumnName="id")
     */
    private $serviceRequest;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

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
     * @param ServiceRequest $serviceRequest
     *
     * @return SmsLog
     */
    public function setServiceRequest($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return SmsLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
