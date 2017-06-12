<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Applicants
 *
 * @ORM\Table(name="applicants")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\ApplicantRepository")
 */
class Applicant
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
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fatherName", type="string", length=255,nullable=true)
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="motherName", type="string", length=255,nullable=true)
     */
    private $motherName;

    /**
     * @var string
     *
     * @ORM\Column(name="spouseName", type="string", length=255,nullable=true)
     */
    private $spouseName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255,nullable=true)
     */
    private $gender;

    /**
     * @var string
     * @Assert\Regex("/^(\d{13}|\d{17})$/")
     * @ORM\Column(name="nid", type="string",nullable=true)
     */
    private $nid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="date",nullable=true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text",nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNo", type="string", length=255,nullable=true)
     */
    private $phoneNo;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\Application", inversedBy="applicant")
     * @ORM\JoinColumn(name="application_id",referencedColumnName="id")
     */
    private $application;


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
     * Set name
     *
     * @param string $name
     * @return Applicant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set fatherName
     *
     * @param string $fatherName
     * @return Applicant
     */
    public function setFatherName($fatherName)
    {
        $this->fatherName = $fatherName;

        return $this;
    }

    /**
     * Get fatherName
     *
     * @return string 
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * Set motherName
     *
     * @param string $motherName
     * @return Applicant
     */
    public function setMotherName($motherName)
    {
        $this->motherName = $motherName;

        return $this;
    }

    /**
     * Get motherName
     *
     * @return string 
     */
    public function getMotherName()
    {
        return $this->motherName;
    }

    /**
     * Set spouseName
     *
     * @param string $spouseName
     * @return Applicant
     */
    public function setSpouseName($spouseName)
    {
        $this->spouseName = $spouseName;

        return $this;
    }

    /**
     * Get spouseName
     *
     * @return string 
     */
    public function getSpouseName()
    {
        return $this->spouseName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Applicant
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;

    }
    /**
     * Get gender
     *
     * @return string 
     */
    public function getBnGender()
    {
        $types = array(
            'Male' => 'পুরুষ',
            'Female' => 'মহিলা',
            'Other' => 'অন্যান্য'
        );
        return isset($types[$this->gender]) ? $types[$this->gender] : "";
    }

    /**
     * Set nid
     *
     * @param integer $nid
     * @return Applicant
     */
    public function setNid($nid)
    {
        $this->nid = $nid;

        return $this;
    }

    /**
     * Get nid
     *
     * @return integer 
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     * @return Applicant
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime 
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Applicant
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * Set address
     *
     * @param string $address
     * @return Applicant
     */
    public function setAddress($address)
    {
        $this->address = $address;

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
     * Set phoneNo
     *
     * @param string $phoneNo
     * @return Applicant
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;

        return $this;
    }

    /**
     * Get phoneNo
     *
     * @return string 
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }
}
