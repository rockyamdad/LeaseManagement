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
 * @ORM\Table(name="service_request", indexes={
 *     @ORM\Index(name="service_request_status_idx", columns={"status"}),
 *     @ORM\Index(name="urgency_idx", columns={"urgency"}),
 *     @ORM\Index(name="delivery_method_idx", columns={"delivery_method"}),
 *     @ORM\Index(name="application_type_idx", columns={"application_type"}),
 *     @ORM\Index(name="application_from_idx", columns={"application_from"}),
 *     @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *     @ORM\Index(name="estimate_delivery_at_idx", columns={"estimate_delivery_at"}),
 *     @ORM\Index(name="delivered_at_idx", columns={"delivered_at"})
 * })
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ServiceRequest\ServiceRequestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceRequest
{
    use ORMBehaviors\Blameable\Blameable;
    use EntityAssistant;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="PorchaProcessingBundle\Generator\ServiceRequestIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="contactNumber", type="string", length=255, nullable=true)
     */
    private $contactNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="nid", type="string", length=255, nullable=true)
     */
    private $nid;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id", nullable=true)
     */
    private $district;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id", nullable=true)
     */
    private $upozila;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="roadNo", type="string", length=255, nullable=true)
     */
    private $roadNo;

    /**
     * @var string
     *
     * @ORM\Column(name="houseNo", type="string", length=255, nullable=true)
     */
    private $houseNo;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="ongoing_care", type="string", length=255, nullable=true)
     */
    private $ongoingCare;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * Values (
    'PENDING',
    'PROCESSING',
    'READY_FOR_DELIVERY',
    'DELIVERED'
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_delivered", type="boolean")
     */
    private $isDelivered = false;

    /**
     * @var string
     * Values (
    'NORMAL',
    'URGENT'
     * )
     * @ORM\Column(name="urgency", type="string", length=255)
     */
    private $urgency;

    /**
     * @var string
     * Values (
    'POSTAL',
    'DIRECT'
     * )
     * @ORM\Column(name="delivery_method", type="string", length=255)
     */
    private $deliveryMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="court_fee", type="decimal")
     */
    private $courtFee = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="total_fee", type="decimal")
     */
    private $totalFee = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_fee", type="decimal")
     */
    private $deliveryFee = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="udc_fee", type="decimal")
     */
    private $udcFee = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_for_delivery_at", type="datetime", nullable=true)
     */
    private $receivedForDeliveryAt;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="received_for_delivery_by", referencedColumnName="id", nullable=true)
     */
    private $receivedForDeliveryBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     */
    private $deliveredAt;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="delivered_by", referencedColumnName="id", nullable=true)
     */
    private $deliveredBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimate_delivery_at", type="date", nullable=true)
     */
    private $estimateDeliveryAt;

    /**
     * @var string
     * Values (
    'PORCHA_REQUEST',
    'INFORMATION_SLIP',
    'CASE_COPY',
    'PORCHA_CORRECTION_REQUEST',
    'MOUZA_MAP'
     * )
     * @ORM\Column(name="application_type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     * Values (
    'WEB',
    'DIRECT',
    'UDC'
     * )
     * @ORM\Column(name="application_from", type="string", length=255)
     */
    private $requestFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    private $detailEntities;
    private $removeEntities = array();

    public function __construct()
    {
        $this->detailEntities = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getIdBn()
    {
        return $this->convertNumber('en2bn', $this->id);
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
     * @return ServiceRequest
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     * @return ServiceRequest
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    /**
     * Get nid
     *
     * @return string
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * Set nid
     *
     * @param string $nid
     * @return ServiceRequest
     */
    public function setNid($nid)
    {
        $this->nid = $nid;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ServiceRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get district
     *
     * @return /AppBundle/Entity/District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set district
     *
     * @param District $district
     * @return ServiceRequest
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get upozila
     *
     * @return \stdClass
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * Set upozila
     *
     * @param AppBundle/Upozila $upozila
     * @return ServiceRequest
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return ServiceRequest
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return ServiceRequest
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get roadNo
     *
     * @return string
     */
    public function getRoadNo()
    {
        return $this->roadNo;
    }

    /**
     * Set roadNo
     *
     * @param string $roadNo
     * @return ServiceRequest
     */
    public function setRoadNo($roadNo)
    {
        $this->roadNo = $roadNo;

        return $this;
    }

    /**
     * Get houseNo
     *
     * @return string
     */
    public function getHouseNo()
    {
        return $this->houseNo;
    }

    /**
     * Set houseNo
     *
     * @param string $houseNo
     * @return ServiceRequest
     */
    public function setHouseNo($houseNo)
    {
        $this->houseNo = $houseNo;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return ServiceRequest
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $created
     * @return ServiceRequest
     */
    public function setCreatedAt($created)
    {
        $this->createdAt = $created;

        return $this;
    }

    /**
     * Set createdBy
     *
     * @param \stdClass $createdBy
     * @return ServiceRequest
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \stdClass 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Get isDelivered
     *
     * @return boolean
     */
    public function getIsDelivered()
    {
        return $this->isDelivered;
    }

    /**
     * Set isDelivered
     *
     * @param boolean $isDelivered
     * @return ServiceRequest
     */
    public function setIsDelivered($isDelivered)
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set urgency
     *
     * @param string $urgency
     * @return ServiceRequest
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get deliveryMethod
     *
     * @return string
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Set deliveryMethod
     *
     * @param string $deliveryMethod
     * @return ServiceRequest
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

    /**
     * Get cortFee
     *
     * @return float
     */
    public function getCourtFee()
    {
        return $this->courtFee;
    }

    /**
     * Set courtFee
     *
     * @param string $courtFee
     * @return ServiceRequest
     */
    public function setCourtFee($courtFee)
    {
        $this->courtFee = $courtFee;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalFee()
    {
        return $this->totalFee;
    }

    /**
     * @param string $totalFee
     *
     * @return ServiceRequest
     */
    public function setTotalFee($totalFee)
    {
        $this->totalFee = $totalFee;

        return $this;
    }

    /**
     * Get deliveryFee
     *
     * @return float
     */
    public function getDeliveryFee()
    {
        return $this->deliveryFee;
    }

    /**
     * Set deliveryFee
     *
     * @param string $deliveryFee
     * @return ServiceRequest
     */
    public function setDeliveryFee($deliveryFee)
    {
        $this->deliveryFee = $deliveryFee;

        return $this;
    }

    /**
     * Get udcFee
     *
     * @return float
     */
    public function getUdcFee()
    {
        return $this->udcFee;
    }

    /**
     * Set udcFee
     *
     * @param string $udcFee
     * @return ServiceRequest
     */
    public function setUdcFee($udcFee)
    {
        $this->udcFee = $udcFee;

        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set deliveredAt
     *
     * @param \DateTime $deliveredAt
     * @return ServiceRequest
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Get estimateDeliveryAt
     *
     * @return \DateTime
     */
    public function getEstimateDeliveryAt()
    {
        return $this->estimateDeliveryAt;
    }

    /**
     * Set estimateDeliveryAt
     *
     * @param \DateTime $estimateDeliveryAt
     * @return ServiceRequest
     */
    public function setEstimateDeliveryAt($estimateDeliveryAt)
    {
        $this->estimateDeliveryAt = $estimateDeliveryAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ServiceRequest
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     *
     * @return ServiceRequest
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDetailEntities()
    {
        return $this->detailEntities;
    }

    /**
     *
     * @param mixed $detailEntities
     * @return ServiceRequest
     */
    public function setDetailEntities($detailEntities)
    {
        $this->detailEntities = $detailEntities;

        return $this;
    }

    /**
     * @param mixed $detailEntity
     */
    public function addDetailEntity($detailEntity)
    {
        $this->detailEntities[] = $detailEntity;
    }

    public function removeDetailEntity($detailEntity)
    {
        $this->removeEntities[] = $detailEntity;
    }

    /**
     * @return string
     */
    public function getOngoingCare()
    {
        return $this->ongoingCare;
    }

    /**
     * @param string $ongoingCare
     *
     * @return ServiceRequest
     */
    public function setOngoingCare($ongoingCare)
    {
        $this->ongoingCare = $ongoingCare;

        return $this;
    }

    public function getRemoveEntities()
    {
        return $this->removeEntities;
    }

    /**
     * @return string
     */
    public function getRequestFrom()
    {
        return $this->requestFrom;
    }

    /**
     * @param string $requestFrom
     *
     * @return ServiceRequest
     */
    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedForDeliveryAt()
    {
        return $this->receivedForDeliveryAt;
    }

    /**
     * @param \DateTime $receivedForDeliveryAt
     *
     * @return ServiceRequest
     */
    public function setReceivedForDeliveryAt($receivedForDeliveryAt)
    {
        $this->receivedForDeliveryAt = $receivedForDeliveryAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceivedForDeliveryBy()
    {
        return $this->receivedForDeliveryBy;
    }

    /**
     * @param mixed $receivedForDeliveryBy
     *
     * @return ServiceRequest
     */
    public function setReceivedForDeliveryBy($receivedForDeliveryBy)
    {
        $this->receivedForDeliveryBy = $receivedForDeliveryBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeliveredBy()
    {
        return $this->deliveredBy;
    }

    /**
     * @param mixed $deliveredBy
     *
     * @return ServiceRequest
     */
    public function setDeliveredBy($deliveredBy)
    {
        $this->deliveredBy = $deliveredBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ServiceRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function statusLabelColor()
    {
        $color = '';
        switch (strtoupper($this->getStatus())) {
            case 'PENDING': $color = 'bg-grey'; break;
            case 'PROCESSING': $color = 'bg-purple'; break;
            case 'RECEIVED': $color = 'bg-yellow'; break;
            case 'READY_FOR_DELIVERY': $color = 'bg-green'; break;
            case 'DELIVERED': $color = 'bg-blue-hoki'; break;
        }

        return $color;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ServiceRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}