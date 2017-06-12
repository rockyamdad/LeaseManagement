<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PorchaProcessingBundle\Entity\PorchaCopyRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * ACLandDocument
 *
 * @ORM\Table(name="copy_Request_files")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AcLandDocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ACLandDocument
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
     * @var PorchaCopyRequest
     *
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\PorchaCopyRequest", inversedBy="documents")
     * @ORM\JoinColumn(name="porchaCopyRequest")
     */
    private $porchaCopyRequest;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="text", nullable= true )
     */
    private $fileType;

    /**
     * @Assert\File(maxSize="6000000")
     */

    public $file;

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;


    public $temp;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));

            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }

    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->preUpload();

        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        $this->file = null;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/acLand/';
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
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
     * Set fileName
     *
     * @param string $fileName
     * @return AcLandDocument
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    




    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return PorchaCopyRequest
     */
    public function getPorchaCopyRequest()
    {
        return $this->porchaCopyRequest;
    }

    /**
     * @param PorchaCopyRequest $porchaCopyRequest
     */
    public function setPorchaCopyRequest($porchaCopyRequest)
    {
        $this->porchaCopyRequest = $porchaCopyRequest;
    }
}
