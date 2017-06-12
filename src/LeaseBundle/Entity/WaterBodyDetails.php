<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WaterBodyDetails
 *
 * @ORM\Table(name="water_body_details")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\WaterBodyDetailsRepository")
 */
class WaterBodyDetails
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
     * @ORM\Column(name="khatianDagNo", type="string", length=255,nullable=true)
     */
    private $khatianDagNo;

    /**
     * @var string
     *
     * @ORM\Column(name="totalAmount", type="string", length=255,nullable=true)
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="proposedAmount", type="string", length=255,nullable=true)
     */
    private $proposedAmount;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Lease", inversedBy="waterBodyDetails")
     * @ORM\JoinColumn(name="lease_id",referencedColumnName="id")
     */
    private $lease;


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
     * Set khatianDagNo
     *
     * @param string $khatianDagNo
     * @return WaterBodyDetails
     */
    public function setKhatianDagNo($khatianDagNo)
    {
        $this->khatianDagNo = $khatianDagNo;

        return $this;
    }

    /**
     * Get khatianDagNo
     *
     * @return string 
     */
    public function getKhatianDagNo()
    {
        return $this->khatianDagNo;
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
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return mixed
     */
    public function getProposedAmount()
    {
        return $this->proposedAmount;
    }

    /**
     * @param mixed $proposedAmount
     */
    public function setProposedAmount($proposedAmount)
    {
        $this->proposedAmount = $proposedAmount;
    }
}
