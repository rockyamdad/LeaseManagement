<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="profiles")
 * @ORM\HasLifecycleCallbacks
 */
class Profile
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
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name_en", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $fullNameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name_bn", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $fullNameBn;

    /**
     * @var string
     *
     * @ORM\Column(name="fathers_full_name_en", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $fathersFullNameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="fathers_full_name_bn", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $fathersFullNameBn;

    /**
     * @var string
     *
     * @ORM\Column(name="mothers_full_name_en", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $mothersFullNameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="mothers_full_name_bn", type="string", length=255 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $mothersFullNameBn;

    /**
     * @var string
     *
     * @ORM\Column(name="nid", type="string", length=50 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $nid;

    /**
     * @var string
     *
     * @ORM\Column(name="brn", type="string", length=100 , nullable=true)
     * @Assert\NotBlank()
     */
    protected $brn;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphone", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    protected $cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    protected $telephone;

    /**
     * @var array $type
     * Values (
    'Male', 'Female', 'Other'
     * )
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    protected $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="dob", type="date", length=255, nullable=true)
     */
    protected $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="religion", type="string", length=255, nullable=true)
     */
    protected $religion;

    /**
     * @var string
     *
     * @ORM\Column(name="education_level", type="string", length=255, nullable=true)
     */
    protected $educationLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="blood_group", type="string", length=255, nullable=true)
     */
    protected $bloodGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="disability", type="string", length=255, nullable=true)
     */
    protected $disability;

    /**
     * @var string
     *
     * @ORM\Column(name="current_address", type="text", nullable=true)
     */
    protected $currentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="permanent_address", type="text", nullable=true)
     */
    protected $permanentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="joining_date", type="date", nullable=true)
     */
    protected $joiningDate;

    /**
     * @var string
     *
     * @ORM\Column(name="joining_date_of_curr_office", type="date", nullable=true)
     */
    protected $joiningDateOfCurrentOffice;

    /**
     * @var string
     *
     * @ORM\Column(name="batch_no", type="string", length=255, nullable=true)
     */
    protected $batchNo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $photo;

    /**
     * @Assert\File(maxSize="5M")
     */
    public $photoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $signature;

    /**
     * @Assert\File(maxSize="5M")
     */
    public $signatureFile;

    public $tempPhoto;
    public $tempSignature;

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
     * Set user
     *
     * @param User $user
     * @return Profile
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set cellphone
     *
     * @param string $cellphone
     * @return Profile
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * Get cellphone
     *
     * @return string
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * Set designation
     *
     * @param string $designation
     * @return Profile
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @return string
     */
    public function getFullNameEn()
    {
        return $this->fullNameEn;
    }

    /**
     * @param string $fullNameEn
     *
     * @return Profile
     */
    public function setFullNameEn($fullNameEn)
    {
        $this->fullNameEn = $fullNameEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullNameBn()
    {
        return $this->fullNameBn;
    }

    /**
     * @param string $fullNameBn
     *
     * @return Profile
     */
    public function setFullNameBn($fullNameBn)
    {
        $this->fullNameBn = $fullNameBn;

        return $this;
    }

    /**
     * @return string
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * @param string $nid
     *
     * @return Profile
     */
    public function setNid($nid)
    {
        $this->nid = $nid;

        return $this;
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
     *
     * @return Profile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param string $dob
     *
     * @return Profile
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo === null ? 'assets/layout3/img/avatar.png' : $this->getWebPhotoPath();
    }

    /**
     * @param mixed $photo
     *
     * @return Profile
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature === null ? 'assets/layout3/img/1x1.png' : $this->getWebSignaturePath();
    }

    /**
     * @param mixed $signature
     *
     * @return Profile
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getSignatureFile()
    {
        return $this->signatureFile;
    }

    /**
     * @param mixed $signatureFile
     *
     * @return Profile
     */
    public function setSignatureFile($signatureFile)
    {
        $this->signatureFile = $signatureFile;

        if (isset($this->signature)) {
            // store the old name to delete after the update
            $this->tempSignature = $this->signature;
        }

        return $this;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setPhotoFile(UploadedFile $file = null)
    {
        $this->photoFile = $file;
        // check if we have an old image path
        if (isset($this->photo)) {
            // store the old name to delete after the update
            $this->tempPhoto = $this->photo;
        }
    }

    private function getRandomFileName()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getPhotoFile()) {
            // do whatever you want to generate a unique name
            $this->photo = $this->getRandomFileName() . '.' . $this->getPhotoFile()->guessExtension();
        }

        if (null !== $this->getSignatureFile()) {
            // do whatever you want to generate a unique name
            $this->signature = $this->getRandomFileName() . '.' . $this->getSignatureFile()->guessExtension();
        }
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null !== $this->getPhotoFile()) {
            $this->getPhotoFile()->move(
                $this->getUploadRootDir(),
                $this->photo
            );
            // check if we have an old image
            if (isset($this->tempPhoto)) {
                // delete the old image
                @unlink($this->getUploadRootDir() . '/' . $this->tempPhoto);
                // clear the temp image path
                $this->tempPhoto = null;
            }
            $this->photoFile = null;
        }

        if (null !== $this->getSignatureFile()) {
            $this->getSignatureFile()->move(
                $this->getUploadRootDir(),
                $this->signature
            );

            // check if we have an old image
            if (isset($this->tempSignature)) {
                // delete the old image
                @unlink($this->getUploadRootDir() . '/' . $this->tempSignature);
                // clear the temp image path
                $this->tempSignature = null;
            }
            $this->signatureFile = null;
        }

    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/users';
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            @unlink($file);
        }
    }

    public function removePhotoFile($file)
    {
        $file_path = $this->getUploadRootDir().'/'.$file;

        if(file_exists($file_path)) unlink($file_path);
    }

    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir() . '/' . $this->photo;
    }

    public function getWebPhotoPath()
    {
        return null === $this->photo
            ? null
            : strpos($this->photo, 'http') === 0 ? $this->photo : $this->getUploadDir() . '/' . $this->photo;
    }

    public function getWebSignaturePath()
    {
        return null === $this->signature
            ? null
            : strpos($this->signature, 'http') === 0 ? $this->signature : $this->getUploadDir() . '/' . $this->signature;
    }

    /**
     * @return string
     */
    public function getFathersFullNameEn()
    {
        return $this->fathersFullNameEn;
    }

    /**
     * @param string $fathersFullNameEn
     *
     * @return Profile
     */
    public function setFathersFullNameEn($fathersFullNameEn)
    {
        $this->fathersFullNameEn = $fathersFullNameEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getFathersFullNameBn()
    {
        return $this->fathersFullNameBn;
    }

    /**
     * @param string $fathersFullNameBn
     *
     * @return Profile
     */
    public function setFathersFullNameBn($fathersFullNameBn)
    {
        $this->fathersFullNameBn = $fathersFullNameBn;

        return $this;
    }

    /**
     * @return string
     */
    public function getMothersFullNameEn()
    {
        return $this->mothersFullNameEn;
    }

    /**
     * @param string $mothersFullNameEn
     *
     * @return Profile
     */
    public function setMothersFullNameEn($mothersFullNameEn)
    {
        $this->mothersFullNameEn = $mothersFullNameEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getMothersFullNameBn()
    {
        return $this->mothersFullNameBn;
    }

    /**
     * @param string $mothersFullNameBn
     *
     * @return Profile
     */
    public function setMothersFullNameBn($mothersFullNameBn)
    {
        $this->mothersFullNameBn = $mothersFullNameBn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrn()
    {
        return $this->brn;
    }

    /**
     * @param mixed $brn
     *
     * @return Profile
     */
    public function setBrn($brn)
    {
        $this->brn = $brn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     *
     * @return Profile
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * @param mixed $religion
     *
     * @return Profile
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducationLevel()
    {
        return $this->educationLevel;
    }

    /**
     * @param mixed $educationLevel
     *
     * @return Profile
     */
    public function setEducationLevel($educationLevel)
    {
        $this->educationLevel = $educationLevel;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
    }

    /**
     * @param mixed $bloodGroup
     *
     * @return Profile
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentAddress()
    {
        return $this->currentAddress;
    }

    /**
     * @param mixed $currentAddress
     *
     * @return Profile
     */
    public function setCurrentAddress($currentAddress)
    {
        $this->currentAddress = $currentAddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermanentAddress()
    {
        return $this->permanentAddress;
    }

    /**
     * @param mixed $permanentAddress
     *
     * @return Profile
     */
    public function setPermanentAddress($permanentAddress)
    {
        $this->permanentAddress = $permanentAddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoiningDate()
    {
        return $this->joiningDate;
    }

    /**
     * @param mixed $joiningDate
     *
     * @return Profile
     */
    public function setJoiningDate($joiningDate)
    {
        $this->joiningDate = $joiningDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoiningDateOfCurrentOffice()
    {
        return $this->joiningDateOfCurrentOffice;
    }

    /**
     * @param mixed $joiningDateOfCurrentOffice
     *
     * @return Profile
     */
    public function setJoiningDateOfCurrentOffice($joiningDateOfCurrentOffice)
    {
        $this->joiningDateOfCurrentOffice = $joiningDateOfCurrentOffice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBatchNo()
    {
        return $this->batchNo;
    }

    /**
     * @param mixed $batchNo
     *
     * @return Profile
     */
    public function setBatchNo($batchNo)
    {
        $this->batchNo = $batchNo;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisability()
    {
        return $this->disability;
    }

    /**
     * @param string $disability
     *
     * @return Profile
     */
    public function setDisability($disability)
    {
        $this->disability = $disability;

        return $this;
    }

}