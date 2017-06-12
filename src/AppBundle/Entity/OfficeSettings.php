<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OfficeSettings
 *
 * @ORM\Table(name="office_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfficeRepository")
 */
class OfficeSettings
{
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Office", inversedBy="officeSettings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="office_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */
    private $office;
    /**
     * @var string
     *
     * @ORM\Column(name="uisc_delivery_fee", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $uiscDeliveryFee;
    /**
     * @var string
     *
     * @ORM\Column(name="uisc_application_receive_fee", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $uiscApplicationReceiveFee;
    /**
     * @var string
     *
     * @ORM\Column(name="uisc_delivery_day", type="string", length=2, nullable=true)
     */
    private $uiscDeliveryDay;
    /**
     * @var string
     *
     * @ORM\Column(name="office_postal_address", type="string", nullable=true)
     */
    private $officePostalAddress;
    /**
     * @var string
     *
     * @ORM\Column(name="application_limit_aday", type="string", nullable=true)
     */
    private $applicationLimitADay;
    /**
     * @var string
     *
     * @ORM\Column(name="post_fee_in_district", type="decimal", nullable=true)
     */
    private $postFeeInDistrict;
    /**
     * @var string
     *
     * @ORM\Column(name="post_fee_out_district", type="decimal", nullable=true)
     */
    private $postFeeOutDistrict;
    /**
     * @var string
     *
     * @ORM\Column(name="union_count", type="smallint", nullable=true)
     */
    private $unionCount;
    /**
     * @var string
     *
     * @ORM\Column(name="mouza_count", type="smallint", nullable=true)
     */
    private $mouzaCount;
    /**
     * @var string
     *
     * @ORM\Column(name="total_record_count", type="integer", nullable=true)
     */
    private $totalRecordCount;

    /**
     * @var string
     *
     * @ORM\Column(name="modules", type="json_array", nullable=true)
     */
    private $modules;
    /**
     * @var string
     *
     * @ORM\Column(name="application_receive_media", type="json_array", nullable=true)
     */
    private $applicationReceiveMedia;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var boolean
     *
     * @ORM\Column(name="workflow_team", type="boolean", nullable=true)
     */
    private $workflowTeam = false;

    /**
     * @return boolean
     */
    public function isWorkflowTeam()
    {
        return $this->workflowTeam;
    }

    /**
     * @param boolean $workflowTeam
     */
    public function setWorkflowTeam($workflowTeam)
    {
        $this->workflowTeam = $workflowTeam;
    }

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
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     */
    public function setOffice($office)
    {
        $this->office = $office;
    }

    /**
     * @return string
     */
    public function getUiscDeliveryFee()
    {
        return $this->uiscDeliveryFee;
    }

    /**
     * @param string $uiscDeliveryFee
     */
    public function setUiscDeliveryFee($uiscDeliveryFee)
    {
        $this->uiscDeliveryFee = (float)$uiscDeliveryFee;
    }

    /**
     * @return string
     */
    public function getUiscApplicationReceiveFee()
    {
        return $this->uiscApplicationReceiveFee;
    }

    /**
     * @param string $uiscApplicationReceiveFee
     */
    public function setUiscApplicationReceiveFee($uiscApplicationReceiveFee)
    {
        $this->uiscApplicationReceiveFee = (float)$uiscApplicationReceiveFee;
    }

    /**
     * @return string
     */
    public function getUiscDeliveryDay()
    {
        return $this->uiscDeliveryDay;
    }

    /**
     * @param string $uiscDeliveryDay
     */
    public function setUiscDeliveryDay($uiscDeliveryDay)
    {
        $this->uiscDeliveryDay = $uiscDeliveryDay;
    }

    /**
     * @return string
     */
    public function getOfficePostalAddress()
    {
        return $this->officePostalAddress;
    }

    /**
     * @param string $officePostalAddress
     */
    public function setOfficePostalAddress($officePostalAddress)
    {
        $this->officePostalAddress = $officePostalAddress;
    }

    /**
     * @return string
     */
    public function getApplicationLimitADay()
    {
        return $this->applicationLimitADay;
    }

    /**
     * @param string $applicationLimitADay
     */
    public function setApplicationLimitADay($applicationLimitADay)
    {
        $this->applicationLimitADay = $applicationLimitADay;
    }

    /**
     * @return string
     */
    public function getPostFeeInDistrict()
    {
        return $this->postFeeInDistrict;
    }

    /**
     * @param string $postFeeInDistrict
     */
    public function setPostFeeInDistrict($postFeeInDistrict)
    {
        $this->postFeeInDistrict = $postFeeInDistrict;
    }

    /**
     * @return string
     */
    public function getPostFeeOutDistrict()
    {
        return $this->postFeeOutDistrict;
    }

    /**
     * @param string $postFeeOutDistrict
     */
    public function setPostFeeOutDistrict($postFeeOutDistrict)
    {
        $this->postFeeOutDistrict = $postFeeOutDistrict;
    }

    /**
     * @return string
     */
    public function getUnionCount()
    {
        return $this->unionCount;
    }

    /**
     * @param string $unionCount
     */
    public function setUnionCount($unionCount)
    {
        $this->unionCount = $unionCount;
    }

    /**
     * @return string
     */
    public function getMouzaCount()
    {
        return $this->mouzaCount;
    }

    /**
     * @param string $mouzaCount
     */
    public function setMouzaCount($mouzaCount)
    {
        $this->mouzaCount = $mouzaCount;
    }

    /**
     * @return string
     */
    public function getTotalRecordCount()
    {
        return $this->totalRecordCount;
    }

    /**
     * @param string $totalRecordCount
     */
    public function setTotalRecordCount($totalRecordCount)
    {
        $this->totalRecordCount = $totalRecordCount;
    }

    /**
     * @return string
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param string $modules
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return string
     */
    public function getApplicationReceiveMedia()
    {
        return $this->applicationReceiveMedia;
    }

    /**
     * @param string $applicationReceiveMedia
     */
    public function setApplicationReceiveMedia($applicationReceiveMedia)
    {
        $this->applicationReceiveMedia = $applicationReceiveMedia;
    }

}