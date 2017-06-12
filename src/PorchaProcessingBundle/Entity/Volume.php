<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Volume
 *
 * @ORM\Table(name="volumes", indexes={
 *     @ORM\Index(name="office_idx", columns={"office_id"}),
 *     @ORM\Index(name="survey_idx", columns={"survey_id"}),
 *     @ORM\Index(name="district_idx", columns={"district_id"}),
 *     @ORM\Index(name="upozila_idx", columns={"upozila_id"})
 * })
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\VolumeRepository")
 */
class Volume
{

    use EntityAssistant;

    /**
     * @ORM\OneToMany(targetEntity="PorchaProcessingBundle\Entity\VolumeMouzas", mappedBy="volume", cascade={"persist", "remove"})
     */
    private $volumeMouzas;

    /**
     * @ORM\OneToMany(targetEntity="PorchaProcessingBundle\Entity\VolumeIndex", mappedBy="volume", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $indexes;
    /**
     * @ORM\OneToMany(targetEntity="PorchaProcessingBundle\Entity\VolumeTemplate", mappedBy="volume")
     */
    private $volumeTemplates;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private $district;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Thana")
     * @ORM\JoinColumn(name="thana_id", referencedColumnName="id")
     */
    private $thana;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id")
     */
    private $upozila;
    /**
     * @var string
     *
     * @ORM\Column(name="pargana", type="string", length=255, nullable=true)
     */
    private $pargana;
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
     * @ORM\Column(name="volume_no", type="string", length=255)
     */
    private $volumeNo;
    /**
     * @var string
     *
     * @ORM\Column(name="canonical_volume_no", type="string", nullable=true)
     */
    private $canonicalvolumeNo;
    /**
     * @var string
     *
     * @ORM\Column(name="no_of_khatians", type="string", length=255, nullable=true)
     */
    private $noOfKhatians;
    /**
     * @var string
     *
     * @ORM\Column(name="last_khatian_nos", type="string", length=255, nullable=true)
     */
    private $lastKhatianNos;
    /**
     * @var string
     *
     * @ORM\Column(name="sub_khatian_nos", type="string", length=255, nullable=true)
     */
    private $subKhatianNos;
    /**
     * @var string
     *
     * @ORM\Column(name="missing_khatian_nos", type="string", length=255, nullable=true)
     */
    private $missingKhatianNos;
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=true, options={"default":0})
     */
    private $approved;
    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", nullable=true, options={"default":0})
     */
    private $archived;
    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;
    /**
     * @var integer
     *
     * @ORM\Column(name="approved_khatian_count", type="integer", nullable=true)
     */
    private $approvedKhatianCount = 0;
    /**
     * @var integer
     *
     * @ORM\Column(name="no_entry_khatian_count", type="integer", nullable=true)
     */
    private $noEntryKhatianCount = 0;

    public function __construct()
    {
        $this->setDeleted(false);
        $this->setArchived(false);
        $this->setApproved(false);
        $this->setApprovedKhatianCount(0);
        $this->volumeMouzas = new ArrayCollection();
        $this->volumeTemplates = new ArrayCollection();
        $this->indexes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getVolumeMouzas()
    {
        return $this->volumeMouzas;
    }

    /**
     * @param mixed $volumeMouzas
     */
    public function setVolumeMouzas($volumeMouzas)
    {
        $this->volumeMouzas = $volumeMouzas;
    }

    /**
     * @param mixed $volumeMouza
     */
    public function addVolumeMouza($volumeMouza)
    {
        if (!$this->volumeMouzas->contains($volumeMouza)) {
            $this->volumeMouzas->add($volumeMouza);
        }
    }

    /**
     * @param mixed $volumeMouza
     */
    public function removeVolumeMouza($volumeMouza)
    {
        if ($this->volumeMouzas->contains($volumeMouza)) {
            $this->volumeMouzas->removeElement($volumeMouza);
        }
    }

    /**
     * @return int
     */
    public function getApprovedKhatianCount()
    {
        return $this->approvedKhatianCount;
    }

    /**
     * @param int $approvedKhatianCount
     */
    public function setApprovedKhatianCount($approvedKhatianCount)
    {
        $this->approvedKhatianCount = $approvedKhatianCount;
    }

    /**
     * @return int
     */
    public function getNoEntryKhatianCount()
    {
        return $this->noEntryKhatianCount;
    }

    /**
     * @param int $noEntryKhatianCount
     */
    public function setNoEntryKhatianCount($noEntryKhatianCount)
    {
        $this->noEntryKhatianCount = $noEntryKhatianCount;
    }

    /**
     * @return string
     */
    public function getCanonicalvolumeNo()
    {
        return $this->canonicalvolumeNo;
    }

    /**
     * @param string $canonicalvolumeNo
     */
    public function setCanonicalvolumeNo($canonicalvolumeNo)
    {
        $this->canonicalvolumeNo = $canonicalvolumeNo;
    }

    /**
     * @return mixed
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param mixed $indexes
     */
    public function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @return mixed
     */
    public function getVolumeTemplates()
    {
        return $this->volumeTemplates;
    }

    /**
     * @param mixed $volumeTemplates
     */
    public function setVolumeTemplates($volumeTemplates)
    {
        $this->volumeTemplates = $volumeTemplates;
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
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @return boolean
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * @param boolean $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    /**
     * @return mixed
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * @param mixed $upozila
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;
    }

    /**
     * @return mixed
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
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getThana()
    {
        return $this->thana;
    }

    /**
     * @param mixed $thana
     */
    public function setThana($thana)
    {
        $this->thana = $thana;
    }

    /**
     * @return string
     */
    public function getPargana()
    {
        return $this->pargana;
    }

    /**
     * @param string $pargana
     */
    public function setPargana($pargana)
    {
        $this->pargana = $pargana;
    }

    /**
     * @return mixed
     */
    public function getVolumeNo()
    {
        return $this->volumeNo;
    }

    /**
     * @param mixed $volumeNo
     */
    public function setVolumeNo($volumeNo)
    {
        $this->volumeNo = $this->convertNumber('en2bn', $volumeNo);
        $this->setCanonicalvolumeNo(str_pad($this->convertNumber('bn2en', $volumeNo), 5, '0', STR_PAD_LEFT));
    }

    /**
     * @return string
     */
    public function getNoOfKhatians()
    {
        return $this->noOfKhatians;
    }

    /**
     * @param string $noOfKhatians
     */
    public function setNoOfKhatians($noOfKhatians)
    {
        $this->noOfKhatians = $noOfKhatians;
    }

    /**
     * @return string
     */
    public function getLastKhatianNos()
    {
        return $this->lastKhatianNos;
    }

    /**
     * @param string $lastKhatianNos
     */
    public function setLastKhatianNos($lastKhatianNos)
    {
        $this->lastKhatianNos = $lastKhatianNos;
    }

    /**
     * @return string
     */
    public function getSubKhatianNos()
    {
        return $this->subKhatianNos;
    }

    /**
     * @param string $subKhatianNos
     */
    public function setSubKhatianNos($subKhatianNos)
    {
        $this->subKhatianNos = $subKhatianNos;
    }

    /**
     * @return string
     */
    public function getMissingKhatianNos()
    {
        return $this->missingKhatianNos;
    }

    /**
     * @param string $missingKhatianNos
     */
    public function setMissingKhatianNos($missingKhatianNos)
    {
        $this->missingKhatianNos = $missingKhatianNos;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }


}
