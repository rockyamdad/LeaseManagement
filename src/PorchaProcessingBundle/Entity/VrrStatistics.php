<?php

namespace PorchaProcessingBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * VrrStatistics
 *
 * @ORM\Table(name="vrr_statistics")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\VrrStatisticsRepository")
 */
class VrrStatistics
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
     * @var integer
     *
     * @ORM\Column(name="total_digitized_khatian", type="bigint",nullable=true)
     */
    private $totalDigitizedKhatian;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_app_received", type="bigint", nullable=true)
     */
    private $totalAppReceived;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_app_delivered", type="bigint", nullable=true)
     */
    private $totalAppDelivered;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_record_room", type="integer", nullable=true)
     */
    private $totalRecordRoom;

    /**
     * @return int
     */
    public function getTotalDigitizedKhatian()
    {
        return $this->totalDigitizedKhatian;
    }

    /**
     * @param int $totalDigitizedKhatian
     */
    public function setTotalDigitizedKhatian($totalDigitizedKhatian)
    {
        $this->totalDigitizedKhatian = $totalDigitizedKhatian;
    }

    /**
     * @return int
     */
    public function getTotalAppReceived()
    {
        return $this->totalAppReceived;
    }

    /**
     * @param int $totalAppReceived
     */
    public function setTotalAppReceived($totalAppReceived)
    {
        $this->totalAppReceived = $totalAppReceived;
    }

    /**
     * @return int
     */
    public function getTotalAppDelivered()
    {
        return $this->totalAppDelivered;
    }

    /**
     * @param int $totalAppDelivered
     */
    public function setTotalAppDelivered($totalAppDelivered)
    {
        $this->totalAppDelivered = $totalAppDelivered;
    }

    /**
     * @return int
     */
    public function getTotalRecordRoom()
    {
        return $this->totalRecordRoom;
    }

    /**
     * @param int $totalRecordRoom
     */
    public function setTotalRecordRoom($totalRecordRoom)
    {
        $this->totalRecordRoom = $totalRecordRoom;
    }


}
