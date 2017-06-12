<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketDetail
 *
 * @ORM\Table(name="market_details")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\MarketDetailRepository")
 */
class MarketDetail
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
     * @ORM\Column(name="shopNo", type="string", length=255,nullable=true)
     */
    private $shopNo;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\Lease",inversedBy="marketDetail")
     * @ORM\JoinColumn(name="lease_id",referencedColumnName="id")
     */
    private $lease;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Market", inversedBy="marketDetails")
     * @ORM\JoinColumn(name="market_id",referencedColumnName="id")
     */
    private $market;


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
     * Set shopNo
     *
     * @param string $shopNo
     * @return MarketDetail
     */
    public function setShopNo($shopNo)
    {
        $this->shopNo = $shopNo;

        return $this;
    }

    /**
     * Get shopNo
     *
     * @return string 
     */
    public function getShopNo()
    {
        return $this->shopNo;
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
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * @param mixed $market
     */
    public function setMarket($market)
    {
        $this->market = $market;
    }

    /**
     * @return string
     */
    public function getKhatianDagNo()
    {
        return $this->khatianDagNo;
    }

    /**
     * @param string $khatianDagNo
     */
    public function setKhatianDagNo($khatianDagNo)
    {
        $this->khatianDagNo = $khatianDagNo;
    }
}
