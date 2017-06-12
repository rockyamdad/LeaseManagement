<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Market
 *
 * @ORM\Table(name="markets")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\MarketRepository")
 */
class Market
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
     * @ORM\Column(name="marketName", type="string", length=255, nullable=false)
     */
    private $marketName;

    /**
     * @return string
     */
    public function getMarketName()
    {
        return $this->marketName;
    }

    /**
     * @param string $marketName
     */
    public function setMarketName($marketName)
    {
        $this->marketName = $marketName;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}
