<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Office;
/**
 * smsSetting
 *
 * @ORM\Table(name="sms_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SmsSettingRepository")
 */
class SmsSetting
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
     * @ORM\Column(name="new_application_message", type="text", nullable=true)
     */
    private $newApplicationMessage;
    /**
     * @var string
     *
     * @ORM\Column(name="delivery_change_message", type="text", nullable=true)
     */
    private $deliveryChangeMessage;
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
    public function getNewApplicationMessage()
    {
        return $this->newApplicationMessage;
    }

    /**
     * @param string $newApplicationMessage
     */
    public function setNewApplicationMessage($newApplicationMessage)
    {
        $this->newApplicationMessage = $newApplicationMessage;
    }

    /**
     * @return string
     */
    public function getDeliveryChangeMessage()
    {
        return $this->deliveryChangeMessage;
    }

    /**
     * @param string $deliveryChangeMessage
     */
    public function setDeliveryChangeMessage($deliveryChangeMessage)
    {
        $this->deliveryChangeMessage = $deliveryChangeMessage;
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