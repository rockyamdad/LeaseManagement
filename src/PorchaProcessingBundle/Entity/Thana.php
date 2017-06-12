<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Thana
 *
 * @ORM\Table(name="khatian_thanas")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\ThanaRepository")
 */
class Thana
{
    use ORMBehaviors\Blameable\Blameable;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\District")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    private $district;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="geocode", type="string", length=2)
     */
    private $geocode;
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=true)
     */
    private $approved;
    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    public function __construct()
    {
        $this->setDeleted(false);
    }

    /**
     * @return string
     */
    public function getGeocode()
    {
        return $this->geocode;
    }

    /**
     * @param string $geocode
     */
    public function setGeocode($geocode)
    {
        $this->geocode = $geocode;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
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
     * @return Upozila
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Upozila
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }
}
