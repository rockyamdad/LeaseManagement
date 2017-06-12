<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Entity\ACLandDocument;
use AppBundle\Entity\Office;
use AppBundle\Traits\EntityAssistant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use UserBundle\Entity\User;

/**
 * PorchaCopyRequest
 * @ORM\Table(name="porcha_copy_requests")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ServiceRequest\PorchaCopyRequestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PorchaCopyRequest
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
     * @var string
     *
     * @ORM\Column(name="requested_volume_khatian", type="text", nullable=true)
     */
    private $requestedVolumeKhatian;

    /**
     * @var string
     *
     * @ORM\Column(name="request_reason", type="text", nullable=true)
     */
    private $requestReason;
    
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255 , nullable=true)
     */
    private $subject;
    
    /**
     * @var string
     *
     * @ORM\Column(name="request_response", type="text", nullable=true)
     */
    private $requestResponse;
    

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="to_office_id", referencedColumnName="id")
     */
    private $toOffice;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ACLandDocument", mappedBy="porchaCopyRequest", cascade={"persist", "remove"})
     */
    private $documents;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", nullable=true)
     */
    private $createdBy;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="replied", type="boolean", nullable=true)
     */
    private $replied = false;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="request_seen", type="boolean", nullable=true)
     */
    private $requestSeen = false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="replied_seen", type="boolean", nullable=true)
     */
    private $repliedSeen = false;
    /**
     * @var string
     * Values (
    'REPLIED_NEEDED',
    'REPLIED'
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * @return boolean
     */
    public function isRequestSeen()
    {
        return $this->requestSeen;
    }

    /**
     * @param boolean $requestSeen
     */
    public function setRequestSeen($requestSeen)
    {
        $this->requestSeen = $requestSeen;
    }
    
    /**
     * @return boolean
     */
    public function isRepliedSeen()
    {
        return $this->repliedSeen;
    }

    /**
     * @param boolean $repliedSeen
     */
    public function setRepliedSeen($repliedSeen)
    {
        $this->repliedSeen = $repliedSeen;
    }
    

    /**
     * @return string
     */
    public function getRequestedVolumeKhatian()
    {
        return $this->requestedVolumeKhatian;
    }

    /**
     * @param string $requestedVolumeKhatian
     */
    public function setRequestedVolumeKhatian($requestedVolumeKhatian)
    {
        $this->requestedVolumeKhatian = $requestedVolumeKhatian;
    }

    /**
     * @return string
     */
    public function getRequestReason()
    {
        return $this->requestReason;
    }

    /**
     * @param string $requestReason
     */
    public function setRequestReason($requestReason)
    {
        $this->requestReason = $requestReason;
    }

    /**
     * @return string
     */
    public function getRequestResponse()
    {
        return $this->requestResponse;
    }

    /**
     * @param string $requestResponse
     */
    public function setRequestResponse($requestResponse)
    {
        $this->requestResponse = $requestResponse;
    }

    /**
     * @return mixed
     */
    public function getToOffice()
    {
        return $this->toOffice;
    }

    /**
     * @param mixed $toOffice
     */
    public function setToOffice($toOffice)
    {
        $this->toOffice = $toOffice;
    }

    /**
     * @return boolean
     */
    public function isReplied()
    {
        return $this->replied;
    }

    /**
     * @param boolean $replied
     */
    public function setReplied($replied)
    {
        $this->replied = $replied;
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
     * @return PorchaCopyRequest
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
     * @return PorchaCopyRequest
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
     * @return Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     *
     * @return PorchaCopyRequest
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
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
     * @return PorchaCopyRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    
    /**
     * @param ArrayCollection $documents
     */
    public function setDocuments($documents)
    {
        foreach ($documents as $document) {
           $document->setPorchaCopyRequest($this);
        }
        $this->documents = $documents;

    }
    /**
     * Remove $documents
     *
     * @param ACLandDocument $documents
     */
    public function removeImage(ACLandDocument $documents)
    {
        $this->documents->removeElement($documents);
    }

    public function addDocument(ACLandDocument $document)
    {
        $this->documents[] = $document;
        $document->setPorchaCopyRequest($this);

        return $this;
    }
    

}