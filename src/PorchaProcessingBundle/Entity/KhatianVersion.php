<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * KhatianVersion
 *
 * @ORM\Table(name="khatian_versions")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\KhatianVersionRepository")
 */
class KhatianVersion
{
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Khatian")
     * @ORM\JoinColumn(name="khatian_id", referencedColumnName="id")
     */
    private $khatian;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    private $editor;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\NonDeliverableMessage")
     * @ORM\JoinColumn(name="non_deliverable_message_id", referencedColumnName="id", nullable=true)
     */
    private $nonDeliverableMessage;
    /**
     * @var string
     *
     * @ORM\Column(name="non_deliverable", type="json_array", nullable=true)
     */
    private $nonDeliverable;
    /**
     * @var integer
     *
     * @ORM\Column(name="version_no", type="smallint")
     */
    private $versionNo;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="edited_at", type="datetime", nullable=true)
     */
    private $editedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var boolean
     *
     * @ORM\Column(name="new_version_required", type="boolean", nullable=true)
     */
    private $newVersionRequired = true;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="bigint", nullable=true)
     */
    private $identifier;

    /**
     * @return mixed
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param mixed $editor
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;
    }

    /**
     * @return mixed
     */
    public function getEditedAt()
    {
        return $this->editedAt;
    }

    /**
     * @param mixed $editedAt
     */
    public function setEditedAt($editedAt)
    {
        $this->editedAt = $editedAt;
    }

    /**
     * @return mixed
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param mixed $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
    }

    /**
     * @return string
     */
    public function getNonDeliverable()
    {
        return $this->nonDeliverable;
    }

    /**
     * @param string $nonDeliverable
     */
    public function setNonDeliverable($nonDeliverable)
    {
        $this->nonDeliverable = $nonDeliverable;
    }

    /**
     * @return boolean
     */
    public function isNewVersionRequired()
    {
        return $this->newVersionRequired;
    }

    /**
     * @param boolean $newVersionRequired
     */
    public function setNewVersionRequired($newVersionRequired)
    {
        $this->newVersionRequired = $newVersionRequired;
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
     * @return Khatian
     */
    public function getKhatian()
    {
        return $this->khatian;
    }

    /**
     * @param mixed $khatian
     */
    public function setKhatian($khatian)
    {
        $this->khatian = $khatian;
    }

    /**
     * @return int
     */
    public function getVersionNo()
    {
        return $this->versionNo;
    }

    /**
     * @param int $versionNo
     */
    public function setVersionNo($versionNo)
    {
        $this->versionNo = $versionNo;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param integer $identifier
     *
     * @return KhatianVersion
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }
}