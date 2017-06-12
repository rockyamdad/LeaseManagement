<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * CitizenUser
 *
 * @ORM\Table(name="citizenUser")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\CitizenUserRepository")
 * @UniqueEntity(fields="phoneNumber", message="ফোন নম্বর ইতিমধ্যেই ব্যবহার করা হয়েছে ")
 */
class CitizenUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
     */
    private $email;
    /**
     * @var string
     *
     * @ORM\Column(name="nationalID", type="string", length=255, nullable=true)
     */
    private $nationalID;
    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255)
     */
    protected $phoneNumber;
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="CitizenProfile",mappedBy="user",cascade={"persist"})
     */
    protected $profile;

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
     * Set email
     *
     * @param string $email
     * @return CitizenUser
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
     * Set password
     *
     * @param string $password
     * @return CitizenUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $profile->setUser($this);
        $this->profile = $profile;
    }

    /**
     * @return string
     */
    public function getNationalID()
    {
        return $this->nationalID;
    }

    /**
     * @param string $nationalID
     */
    public function setNationalID($nationalID)
    {
        $this->nationalID = $nationalID;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }


}
