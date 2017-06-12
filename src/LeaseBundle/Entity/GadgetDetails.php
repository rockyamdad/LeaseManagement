<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GadgetDetail
 *
 * @ORM\Table(name="gadget_details")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\GadgetDetailRepository")
 */
class GadgetDetails
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
     *
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Khatian")
     * @ORM\JoinColumn(name="saKhatianNo",referencedColumnName="id",nullable=true)
     */
    private $saKhatianNo;

    /**
     * @var string
     *
     * @ORM\Column(name="saDagNo", type="string", length=255,nullable=true)
     */
    private $saDagNo;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Khatian")
     * @ORM\JoinColumn(name="rsKhatianNo",referencedColumnName="id",nullable=true)
     */
    private $rsKhatianNo;

    /**
     * @var string
     *
     * @ORM\Column(name="rsDagNo", type="string", length=255,nullable=true)
     */
    private $rsDagNo;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", length=255,nullable=true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="proposedAmount", type="float", length=255,nullable=true)
     */
    private $proposedAmount;


    /**
     * @var string
     *
     * @ORM\Column(name="propertyType", type="string", length=255,nullable=true)
     */
    private $propertyType;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Gadget", inversedBy="gadgetDetails")
     * @ORM\JoinColumn(name="gadget_id",referencedColumnName="id")
     */
    protected $gadget;


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
     * Set saDagNo
     *
     * @param string $saDagNo
     * @return GadgetDetails
     */
    public function setSaDagNo($saDagNo)
    {
        $this->saDagNo = $saDagNo;

        return $this;
    }

    /**
     * Get saDagNo
     *
     * @return string 
     */
    public function getSaDagNo()
    {
        return $this->saDagNo;
    }

    /**
     * Set rsDagNo
     *
     * @param string $rsDagNo
     * @return GadgetDetails
     */
    public function setRsDagNo($rsDagNo)
    {
        $this->rsDagNo = $rsDagNo;

        return $this;
    }

    /**
     * Get rsDagNo
     *
     * @return string 
     */
    public function getRsDagNo()
    {
        return $this->rsDagNo;
    }

    /**
     * Set propertyType
     *
     * @param string $propertyType
     * @return GadgetDetails
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * Get propertyType
     *
     * @return string 
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @return mixed
     */
    public function getGadget()
    {
        return $this->gadget;
    }

    /**
     * @param mixed $gadget
     */
    public function setGadget($gadget)
    {
        $this->gadget = $gadget;
    }

    /**
     * @return float
     */
    public function getProposedAmount()
    {
        return $this->proposedAmount;
    }

    /**
     * @param float $proposedAmount
     */
    public function setProposedAmount($proposedAmount)
    {
        $this->proposedAmount = $proposedAmount;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return mixed
     */
    public function getSaKhatianNo()
    {
        return $this->saKhatianNo;
    }

    /**
     * @param mixed $saKhatianNo
     */
    public function setSaKhatianNo($saKhatianNo)
    {
        $this->saKhatianNo = $saKhatianNo;
    }

    /**
     * @return mixed
     */
    public function getRsKhatianNo()
    {
        return $this->rsKhatianNo;
    }

    /**
     * @param mixed $rsKhatianNo
     */
    public function setRsKhatianNo($rsKhatianNo)
    {
        $this->rsKhatianNo = $rsKhatianNo;
    }


}
