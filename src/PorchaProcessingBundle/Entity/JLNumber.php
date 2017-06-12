<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JLNumber
 *
 * @ORM\Table(name="jlnumbers", indexes={@ORM\Index(name="survey_type_idx", columns={"survey_type"})})
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\JLNumberRepository")
 */
class JLNumber
{
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Mouza", inversedBy="jlnumbers")
     * @ORM\JoinColumn(name="mouza_id", referencedColumnName="id")
     */
    private $mouza;
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @var array $status
     * Values (
     'CS',
     'SA',
     'RS',
     'BS',
     'DIARA'
     * )
     * @ORM\Column(name="survey_type", type="string", length=15)
     */
    private $surveyType;
    /**
     * @var string
     *
     * @ORM\Column(name="district", type="string", length=255, nullable=true)
     */
    private $district;
    /**
     * @var string
     *
     * @ORM\Column(name="thana", type="string", length=255, nullable=true)
     */
    private $thana;
    /**
     * @var string
     *
     * @ORM\Column(name="division", type="string", length=255, nullable=true)
     */
    private $division;
    /**
     * @var string
     *
     * @ORM\Column(name="mouza_name", type="string", length=255, nullable=true)
     */
    private $mouzaName;
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;
    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;

    public function __construct()
    {
        $this->setDeleted(false);
        $this->setApproved(true);
    }

    /**
     * @return string
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param string $division
     */
    public function setDivision($division)
    {
        $this->division = $division;
    }

    /**
     * @return string
     */
    public function getMouzaName()
    {
        return $this->mouzaName;
    }

    /**
     * @param string $mouzaName
     */
    public function setMouzaName($mouzaName)
    {
        $this->mouzaName = $mouzaName;
    }

    public function __toString()
    {
        return $this->getName();
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
     * @return array
     */
    public function getSurveyType()
    {
        return $this->surveyType;
    }

    /**
     * @param array $surveyType
     */
    public function setSurveyType($surveyType)
    {
        $this->surveyType = $surveyType;
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
}
