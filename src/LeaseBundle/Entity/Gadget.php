<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Gadget
 *
 * @ORM\Table(name="gadgets")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\GadgetRepository")
 */
class Gadget
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
     * @Assert\NotBlank()
     * @ORM\Column(name="caseFileNo", type="string", length=255)
     */
    private $caseFileNo;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="govtAquiredDate", type="date",nullable=true)
     */
    private $govtAquiredDate;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="orginalOwnerName", type="string", length=255,nullable=true)
     */
    private $orginalOwnerName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="fatherName", type="string", length=255,nullable=true)
     */
    private $fatherName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="address", type="text",nullable=true)
     */
    private $address;

    /**
     *
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Mouza")
     * @ORM\JoinColumn(name="mouza_id",referencedColumnName="id",nullable=true)
     */
    private $mouza;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255,nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\GadgetDetails", mappedBy="gadget", cascade={"persist", "remove"})
     */
    protected $gadgetDetails;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\Lease", mappedBy="gadget", cascade={"persist", "remove"})
     */
    public $leases;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\Lease",inversedBy="gadget")
     * @ORM\JoinColumn(name="active_lease_id",referencedColumnName="id",nullable=true)
     */
    private $lease;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDateTime", type="datetime",nullable=true)
     */
    private $createdDateTime;

    public function __construct()
    {
        $this->gadgetDetails = new ArrayCollection();
        $this->leases = new ArrayCollection();
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
     * Set caseFileNo
     *
     * @param string $caseFileNo
     * @return Gadget
     */
    public function setCaseFileNo($caseFileNo)
    {
        $this->caseFileNo = $caseFileNo;

        return $this;
    }

    /**
     * Get caseFileNo
     *
     * @return string 
     */
    public function getCaseFileNo()
    {
        return $this->caseFileNo;
    }

    /**
     * Set govtAquiredDate
     *
     * @param \DateTime $govtAquiredDate
     * @return Gadget
     */
    public function setGovtAquiredDate($govtAquiredDate)
    {
        $this->govtAquiredDate = $govtAquiredDate;

        return $this;
    }

    /**
     * Get govtAquiredDate
     *
     * @return \DateTime 
     */
    public function getGovtAquiredDate()
    {
        return $this->govtAquiredDate;
    }

    /**
     * Set orginalOwnerName
     *
     * @param string $orginalOwnerName
     * @return Gadget
     */
    public function setOrginalOwnerName($orginalOwnerName)
    {
        $this->orginalOwnerName = $orginalOwnerName;

        return $this;
    }

    /**
     * Get orginalOwnerName
     *
     * @return string 
     */
    public function getOrginalOwnerName()
    {
        return $this->orginalOwnerName;
    }

    /**
     * Set fatherName
     *
     * @param string $fatherName
     * @return Gadget
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
     * Set address
     *
     * @param string $address
     * @return Gadget
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
     * Set status
     *
     * @param string $status
     * @return Gadget
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $gadgetDetail
     */
    public function addGadgetDetail($gadgetDetail)
    {
        if (!$this->gadgetDetails->contains($gadgetDetail)) {
            $gadgetDetail->setGadget($this);
            $this->gadgetDetails->add($gadgetDetail);
        }
    }

    /**
     * @param mixed $gadgetDetail
     */
    public function removeGadgetDetail($gadgetDetail)
    {
        if ($this->gadgetDetails->contains($gadgetDetail)) {
            $this->gadgetDetails->removeElement($gadgetDetail);
        }
    }

    /**
     * @param mixed $lease
     */
    public function addLease($lease)
    {
        if (!$this->leases->contains($lease)) {
            $lease->setGadget($this);
            $this->leases->add($lease);
        }
    }

    /**
     * @param mixed $lease
     */
    public function removeLease($lease)
    {
        if ($this->leases->contains($lease)) {
            $this->leases->removeElement($lease);
        }
    }

    /**
     * @return mixed
     */
    public function getGadgetDetails()
    {
        return $this->gadgetDetails;
    }

    /**
     * @return Lease[]
     */
    public function getLeases()
    {
        return $this->leases;
    }

    /**
     * @return mixed
     */
    public function getLease()
    {
        return $this->lease;
    }

    /**
     * @param mixed $lease
     */
    public function setLease($lease)
    {
        $this->lease = $lease;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDateTime()
    {
        return $this->createdDateTime;
    }

    /**
     * @param \DateTime $createdDateTime
     */
    public function setCreatedDateTime($createdDateTime)
    {
        $this->createdDateTime = $createdDateTime;
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

}
