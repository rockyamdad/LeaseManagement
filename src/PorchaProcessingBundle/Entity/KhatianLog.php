<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * KhatianLog
 *
 * @ORM\Table(name="khatian_logs", indexes={@ORM\Index(name="khatian_status_idx", columns={"khatian_status"})})
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\KhatianLogRepository")
 */
class KhatianLog
{
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianVersion")
     * @ORM\JoinColumn(name="khatian_version_id", referencedColumnName="id")
     */
    private $khatianVersion;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="entry_operator_id", referencedColumnName="id", nullable=true)
     */
    private $entryOperator;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="verifier_id", referencedColumnName="id", nullable=true)
     */
    private $verifier;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="comparer_id", referencedColumnName="id", nullable=true)
     */
    private $comparer;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="approver_id", referencedColumnName="id", nullable=true)
     */
    private $approver;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\NonDeliverableMessage")
     * @ORM\JoinColumn(name="non_deliverable_message_id", referencedColumnName="id", nullable=true)
     */
    private $nonDeliverableMessage;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entry_at", type="datetime", nullable=true)
     */
    private $entryAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="verified_at", type="datetime", nullable=true)
     */
    private $verifiedAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="compared_at", type="datetime", nullable=true)
     */
    private $comparedAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var array $khatianStatus
     * Values (
     * 'NONE',
     * 'HAS_ENTRY',
     * 'DRAFT',
     * 'READY_FOR_VERIFICATION',
     * 'READY_FOR_COMPARISON',
     * 'READY_FOR_APPROVAL',
     * 'CORRECTION_REQUIRED',
     * 'APPROVED',
     * 'ARCHIVED'
     * )
     * @ORM\Column(name="khatian_status", type="string", length=255)
     */
    private $khatianStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="batch", type="boolean", nullable=true)
     */
    private $batch;
    /**
     * @var boolean
     *
     * @ORM\Column(name="first_app", type="boolean", nullable=true)
     */
    private $firstApp;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="locked_by_id", referencedColumnName="id", nullable=true)
     */
    private $lockedBy;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="locked_at", type="datetime", nullable=true)
     */
    private $lockedAt;

    /**
     * @return boolean
     */
    public function isFirstApp()
    {
        return $this->firstApp;
    }

    /**
     * @param boolean $firstApp
     */
    public function setFirstApp($firstApp)
    {
        $this->firstApp = $firstApp;
    }

    /**
     * @return boolean
     */
    public function isBatch()
    {
        return $this->batch;
    }

    /**
     * @param boolean $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }

    /**
     * @return KhatianVersion
     */
    public function getKhatianVersion()
    {
        return $this->khatianVersion;
    }

    /**
     * @param mixed $khatianVersion
     */
    public function setKhatianVersion($khatianVersion)
    {
        $this->khatianVersion = $khatianVersion;
    }

    /**
     * @return array
     */
    public function getKhatianStatus()
    {
        return $this->khatianStatus;
    }

    /**
     * @param array $khatianStatus
     */
    public function setKhatianStatus($khatianStatus)
    {
        $this->khatianStatus = $khatianStatus;
    }

    public function getKhatianStatusView()
    {
        switch (strtoupper($this->khatianStatus)) {

            case 'DRAFT':
                return 'খসড়া ';
            case 'READY_FOR_VERIFICATION':
                return 'যাচাইয়ের অপেক্ষায়';
            case 'CORRECTION_REQUIRED':
                return 'সংশোধন প্রয়োজন ';
            case 'READY_FOR_COMPARISON':
                return 'তুলনার অপেক্ষায়';
            case 'READY_FOR_APPROVAL':
                return 'অনুমোদনের অপেক্ষায়';
            case 'APPROVED':
                return 'অনুমোদিত ';
            case 'ARCHIVED':
                return 'আর্কাইভ ';
            case 'HAS_ENTRY':
                return 'এন্ট্রি আছে ';
            default:
                return 'এন্ট্রি নাই ';
        }
    }

    public function getKhatianStatusColor()
    {
        switch (strtoupper($this->khatianStatus)) {

            case 'DRAFT':
                return 'bg-grey';
            case 'READY_FOR_VERIFICATION':
                return 'bg-yellow';
            case 'READY_FOR_COMPARISON':
                return 'bg-yellow-gold';
            case 'READY_FOR_APPROVAL':
                return 'bg-yellow-casablanca';
            case 'CORRECTION_REQUIRED':
                return 'bg-grey-cascade';
            case 'APPROVED':
                return 'bg-green';
            case 'ARCHIVED':
                return 'bg-green-seagreen';
            case 'HAS_ENTRY':
                return 'bg-purple-medium';
            case 'WAITING':
                return 'bg-blue-hoki';
            default:
                return 'bg-red';
        }
    }

    /**
     * @return mixed
     */
    public function getNonDeliverableMessage()
    {
        return $this->nonDeliverableMessage;
    }

    /**
     * @param mixed $nonDeliverableMessage
     */
    public function setNonDeliverableMessage($nonDeliverableMessage)
    {
        $this->nonDeliverableMessage = $nonDeliverableMessage;
    }

    /**
     * @return mixed
     */
    public function getEntryOperator()
    {
        return $this->entryOperator;
    }

    /**
     * @param mixed $entryOperator
     */
    public function setEntryOperator($entryOperator)
    {
        $this->entryOperator = $entryOperator;
    }

    /**
     * @return mixed
     */
    public function getVerifier()
    {
        return $this->verifier;
    }

    /**
     * @param mixed $verifier
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @return mixed
     */
    public function getComparer()
    {
        return $this->comparer;
    }

    /**
     * @param mixed $comparer
     */
    public function setComparer($comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * @return mixed
     */
    public function getApprover()
    {
        return $this->approver;
    }

    /**
     * @param mixed $approver
     */
    public function setApprover($approver)
    {
        $this->approver = $approver;
    }

    /**
     * @return \DateTime
     */
    public function getEntryAt()
    {
        return $this->entryAt;
    }

    /**
     * @param \DateTime $entryAt
     */
    public function setEntryAt($entryAt)
    {
        $this->entryAt = $entryAt;
    }

    /**
     * @return \DateTime
     */
    public function getVerifiedAt()
    {
        return $this->verifiedAt;
    }

    /**
     * @param \DateTime $verifiedAt
     */
    public function setVerifiedAt($verifiedAt)
    {
        $this->verifiedAt = $verifiedAt;
    }

    /**
     * @return \DateTime
     */
    public function getComparedAt()
    {
        return $this->comparedAt;
    }

    /**
     * @param \DateTime $comparedAt
     */
    public function setComparedAt($comparedAt)
    {
        $this->comparedAt = $comparedAt;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getLockedAt()
    {
        return $this->lockedAt;
    }

    /**
     * @param \DateTime $lockedAt
     *
     * @return Khatian
     */
    public function setLockedAt($lockedAt)
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    public function isLocked()
    {
        return $this->getLockedBy() ? true : false;
    }

    /**
     * @return mixed
     */
    public function getLockedBy()
    {
        return $this->lockedBy;
    }

    /**
     * @param mixed $lockedBy
     *
     * @return Khatian
     */
    public function setLockedBy($lockedBy)
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }
}