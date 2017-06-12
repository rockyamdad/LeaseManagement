<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VolumeIndex
 *
 * @ORM\Table(name="volume_indexes", indexes={
 *     @ORM\Index(name="volume_idx", columns={"volume_id"}),
 * })
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\VolumeIndexRepository")
 */
class VolumeIndex
{
    use ORMBehaviors\Blameable\Blameable;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Volume", inversedBy="indexes")
     * @ORM\JoinColumn(name="volume_id", referencedColumnName="id")
     */
    private $volume;
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
     * @ORM\Column(name="hal_dag_no", type="string", length=255, nullable=true)
     */
    private $halDagNo;
    /**
     * @var string
     *
     * @ORM\Column(name="sabek_dag_no", type="string", length=255, nullable=true)
     */
    private $sabekDagNo;
    /**
     * @var string
     *
     * @ORM\Column(name="khatian_no", type="string", length=255)
     */
    private $khatianNo;
    /**
     * @var string
     *
     * @ORM\Column(name="land_quantity_acre", type="string", length=255, nullable=true)
     */
    private $landQuantityAcre;
    /**
     * @var string
     *
     * @ORM\Column(name="land_quantity_decimal", type="string", length=255, nullable=true)
     */
    private $landQuantityDecimal;
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
     * @var string
     *
     * @ORM\Column(name="index_comment", type="text", nullable=true)
     */
    private $comment;

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
    public function getHalDagNo()
    {
        return $this->halDagNo;
    }

    /**
     * @param string $halDagNo
     */
    public function setHalDagNo($halDagNo)
    {
        $this->halDagNo = $halDagNo;
    }

    /**
     * @return string
     */
    public function getSabekDagNo()
    {
        return $this->sabekDagNo;
    }

    /**
     * @param string $sabekDagNo
     */
    public function setSabekDagNo($sabekDagNo)
    {
        $this->sabekDagNo = $sabekDagNo;
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
        $this->khatianNo = $khatianNo;
    }

    /**
     * @return string
     */
    public function getLandQuantityAcre()
    {
        return $this->landQuantityAcre;
    }

    /**
     * @param string $landQuantityAcre
     */
    public function setLandQuantityAcre($landQuantityAcre)
    {
        $this->landQuantityAcre = $landQuantityAcre;
    }

    /**
     * @return string
     */
    public function getLandQuantityDecimal()
    {
        return $this->landQuantityDecimal;
    }

    /**
     * @param string $landQuantityDecimal
     */
    public function setLandQuantityDecimal($landQuantityDecimal)
    {
        $this->landQuantityDecimal = $landQuantityDecimal;
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
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



}
