<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UdcEntrepreneur
 *
 * @ORM\Table(name="udc_entrepreneur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UdcEntrepreneurRepository")
 */
class UdcEntrepreneur
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Udc",inversedBy="udcEntrepreneurs")
     * @ORM\JoinColumn(name="udc_id", referencedColumnName="id", nullable=true)
     */
    private $udc;

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
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="entrepreneur_type", type="string", nullable=true)
     */
    private $entrepreneurType;


    /**
     * @var string
     *
     * @ORM\Column(name="father_name", type="string", nullable=true)
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="educational_qualification", type="string", nullable=true)
     */
    private $educationalQualification;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_no", type="string", nullable=true)
     */
    private $mobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var array $gender
     * Values (
    'MALE',
    'FEMALE',
    'OTHER'
     * )
     * @ORM\Column(name="gender", type="string", length=255)
     */
    private $gender;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return mixed
     */
    public function getUdc()
    {
        return $this->udc;
    }

    /**
     * @param mixed $udc
     */
    public function setUdc($udc)
    {
        $this->udc = $udc;
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
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * @param string $fatherName
     */
    public function setFatherName($fatherName)
    {
        $this->fatherName = $fatherName;
    }

    /**
     * @return string
     */
    public function getEducationalQualification()
    {
        return $this->educationalQualification;
    }

    /**
     * @param string $educationalQualification
     */
    public function setEducationalQualification($educationalQualification)
    {
        $this->educationalQualification = $educationalQualification;
    }

    /**
     * @return string
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    /**
     * @param string $mobileNo
     */
    public function setMobileNo($mobileNo)
    {
        $this->mobileNo = $mobileNo;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return array
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param array $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getEntrepreneurType()
    {
        return $this->entrepreneurType;
    }

    /**
     * @param string $entrepreneurType
     */
    public function setEntrepreneurType($entrepreneurType)
    {
        $this->entrepreneurType = $entrepreneurType;
    }


}