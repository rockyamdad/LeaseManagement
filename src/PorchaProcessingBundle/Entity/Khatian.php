<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Entity\Office;
use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Khatian
 *
 * @ORM\Table(name="khatians", indexes={
 *     @ORM\Index(name="khatian_idx", columns={"status"}),
 *     @ORM\Index(name="mouza_idx", columns={"mouza_id"}),
 *     @ORM\Index(name="office_idx", columns={"office_id"}),
 *     @ORM\Index(name="jlnumber_idx", columns={"jlnumber_id"})
 * })
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\KhatianRepository")
 */
class Khatian
{
    use EntityAssistant;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Volume")
     * @ORM\JoinColumn(name="volume_id", referencedColumnName="id")
     */
    private $volume;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\VolumeTemplate")
     * @ORM\JoinColumn(name="volume_template_id", referencedColumnName="id")
     */
    private $volumeTemplate;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Mouza")
     * @ORM\JoinColumn(name="mouza_id", referencedColumnName="id", nullable=true)
     */
    private $mouza;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\JLNumber")
     * @ORM\JoinColumn(name="jlnumber_id", referencedColumnName="id", nullable=true)
     */
    private $jlnumber;

    /**
     * @var string
     */
    private $district;
    /**
     * @var string
     */
    private $thana;
    /**
     * @var string
     */
    private $upozila;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;
    /**
     * @ORM\OneToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianVersion")
     * @ORM\JoinColumn(name="last_version_id", referencedColumnName="id", nullable=true)
     */
    private $lastVersion;
    /**
     * @var array $status
     * Values (
     'NONE',
     'DRAFT',
     'READY_FOR_VERIFICATION',
     'READY_FOR_COMPARISON',
     'READY_FOR_APPROVAL',
     'CORRECTION_REQUIRED',
     'APPROVED'
     * )
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", nullable=true)
     */
    private $archived;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    private $archivedAt;
    /**
     * @var integer
     *
     * @ORM\Column(name="correction_cycle", type="smallint")
     */
    private $correctionCycle;
    /**
     * @var string
     *
     * @ORM\Column(name="khatian_no", type="string", nullable=true)
     */
    private $khatianNo;
    /**
     * @var string
     *
     * @ORM\Column(name="canonical_khatian_no", type="string", nullable=true)
     */
    private $canonicalKhatianNo;
    /**
     * @var string
     *
     * @ORM\Column(name="rs_no", type="string", nullable=true)
     */
    private $rsNo;
    /**
     * @var string
     *
     * @ORM\Column(name="pargana", type="string", nullable=true)
     */
    private $pargana;
    /**
     * @var string
     *
     * @ORM\Column(name="taugi_no", type="string", nullable=true)
     */
    private $taugiNo;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
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
     * @var string
     *
     * @ORM\Column(name="mouza_map_reference", type="string", nullable=true)
     */
    private $mouzaMapReference;
    /**
     * @var boolean
     *
     * @ORM\Column(name="display_restricted", type="boolean", nullable=true)
     */
    private $displayRestricted;
    /**
     * @var boolean
     *
     * @ORM\Column(name="batch", type="boolean", nullable=true)
     */
    private $batch;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;
    /**
     * @var boolean
     *
     * @ORM\Column(name="re_correction", type="boolean", nullable=true)
     */
    private $reCorrection = 0;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="re_correction_at", type="datetime", nullable=true)
     */
    private $reCorrectionAt;

    /**
     * @return \DateTime
     */
    public function getReCorrectionAt()
    {
        return $this->reCorrectionAt;
    }

    /**
     * @param \DateTime $reCorrectionAt
     */
    public function setReCorrectionAt($reCorrectionAt)
    {
        $this->reCorrectionAt = $reCorrectionAt;
    }

    /**
     * @return \DateTime
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * @param \DateTime $archivedAt
     */
    public function setArchivedAt($archivedAt)
    {
        $this->archivedAt = $archivedAt;
    }

    /**
     * @return boolean
     */
    public function isReCorrection()
    {
        return $this->reCorrection;
    }

    /**
     * @param boolean $reCorrection
     */
    public function setReCorrection($reCorrection)
    {
        $this->reCorrection = $reCorrection;
    }

    /**
     * @return string
     */
    public function getCanonicalKhatianNo()
    {
        return $this->canonicalKhatianNo;
    }

    /**
     * @param string $canonicalKhatianNo
     */
    public function setCanonicalKhatianNo($canonicalKhatianNo)
    {
        $this->canonicalKhatianNo = $canonicalKhatianNo;
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
     * @return boolean
     */
    public function isDisplayRestricted()
    {
        return $this->displayRestricted;
    }

    /**
     * @param boolean $displayRestricted
     */
    public function setDisplayRestricted($displayRestricted)
    {
        $this->displayRestricted = $displayRestricted;
    }

    /**
     * @return mixed
     */
    public function getVolumeTemplate()
    {
        return $this->volumeTemplate;
    }

    /**
     * @param mixed $volumeTemplate
     */
    public function setVolumeTemplate($volumeTemplate)
    {
        $this->volumeTemplate = $volumeTemplate;
    }

    /**
     * @return string
     */
    public function getTaugiNo()
    {
        return $this->taugiNo;
    }

    /**
     * @param string $taugiNo
     */
    public function setTaugiNo($taugiNo)
    {
        $this->taugiNo = $taugiNo;
    }

    /**
     * @return string
     */
    public function getRsNo()
    {
        return $this->rsNo;
    }

    /**
     * @param string $rsNo
     */
    public function setRsNo($rsNo)
    {
        $this->rsNo = $rsNo;
    }

    /**
     * @return mixed
     */
    public function getJlnumber()
    {
        return $this->jlnumber;
    }

    /**
     * @param mixed $jlnumber
     */
    public function setJlnumber($jlnumber)
    {
        $this->jlnumber = $jlnumber;
    }

    /**
     * @return string
     */
    public function getMouzaMapReference()
    {
        return $this->mouzaMapReference;
    }

    /**
     * @param string $mouzaMapReference
     */
    public function setMouzaMapReference($mouzaMapReference)
    {
        $this->mouzaMapReference = $mouzaMapReference;
    }

    /**
     * @return mixed
     */
    public function getMouza()
    {
        return $this->mouza;
    }

    /**
     * @param mixed $mouza
     */
    public function setMouza($mouza)
    {
        $this->mouza = $mouza;
    }

    /**
     * @return string
     */
    public function getKhatianNo()
    {
        return $this->khatianNo;
    }

    /**
     * @param string $khatianNo
     */
    public function setKhatianNo($khatianNo)
    {
        $this->khatianNo = $this->convertNumber('en2bn', $khatianNo);
        $this->setCanonicalKhatianNo(str_pad($this->convertNumber('bn2en', $khatianNo), 5, '0', STR_PAD_LEFT));
    }

    /**
     * @return int
     */
    public function getCorrectionCycle()
    {
        return $this->correctionCycle;
    }

    /**
     * @param int $correctionCycle
     */
    public function setCorrectionCycle($correctionCycle)
    {
        $this->correctionCycle = $correctionCycle;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatusView()
    {
        switch ($this->status) {
            case 'DRAFT':
                return 'খসড়া ';
            case 'READY_FOR_VERIFICATION':
                return 'যাচাইয়ের অপেক্ষায়';
            case 'CORRECTION_REQUIERED':
                return 'সংশোধন প্রয়োজন ';
            case 'READY_FOR_COMPARISON':
                return 'তুলনার অপেক্ষায়';
            case 'READY_FOR_APPROVAL':
                return 'অনুমোদনের অপেক্ষায়';
            case 'APPROVED':
                return 'অনুমোদিত ';
            case 'ARCHIVED':
                return 'আর্কাইভ ';
            default:
                return 'N/A';
        }
    }

    /**
     * @return KhatianVersion
     */
    public function getLastVersion()
    {
        return $this->lastVersion;
    }

    /**
     * @param mixed $lastVersion
     */
    public function setLastVersion($lastVersion)
    {
        $this->lastVersion = $lastVersion;
    }

    /**
     * @return mixed
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getThana()
    {
        return $this->thana;
    }

    /**
     * @param string $thana
     */
    public function setThana($thana)
    {
        $this->thana = $thana;
    }

    /**
     * @return string
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * @param string $upozila
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;
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
     */
    public function setOffice($office)
    {
        $this->office = $office;
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