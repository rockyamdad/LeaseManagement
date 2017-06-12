<?php

namespace LeaseBundle\Entity;

use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use EasyBanglaDate\Types\BnDateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Lease
 *
 * @ORM\Table(name="leases")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\LeaseRepository")
 */
class Lease
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
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="tender", type="string", length=255,nullable=true)
     */
    private $tender;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;

    /**
     *
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Khatian")
     * @ORM\JoinColumn(name="khatian_id",referencedColumnName="id",nullable=true)
     */
    private $khatian;

    /**
     * @var string
     *
     * @ORM\Column(name="shotangso", type="string", length=255,nullable=true)
     */
    private $shotangso;
    /**
     * @var string
     *
     * @ORM\Column(name="fiscalyear", type="string", length=255,nullable=true)
     */
    private $fiscalyear;

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="date",nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="end_date", type="date",nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\WaterBodyDetails", mappedBy="lease", cascade={"persist", "remove"})
     */
    private $waterBodyDetails;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\History", mappedBy="leaseId")
     */
    private $histories;

    /**
     * @ORM\OneToOne(targetEntity="LeaseBundle\Entity\MarketDetail", mappedBy="lease", cascade={"persist", "remove"})
     */
    protected $marketDetail;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id",type="integer",nullable=true)
     */
    private $group;

    /**
     * @var string
     *
     * @ORM\Column(name="status",type="string",length=255,nullable=true)
     */
    private $status;


    /**
     * @var string
     *
     * @ORM\Column(name="remarks",type="text",nullable=true)
     */
    private $remarks;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Gadget",inversedBy="leases")
     * @ORM\JoinColumn(name="gadget_id",referencedColumnName="id",nullable=true)
     */
    private $gadget;

    /**
     * @ORM\OneToMany(targetEntity="LeaseBundle\Entity\Application", mappedBy="lease", cascade={"persist", "remove"})
     */
    private $applications;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Application",inversedBy="lease")
     * @ORM\JoinColumn(name="application_id",referencedColumnName="id",nullable=true)
     */
    private $application;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDateTime", type="datetime",nullable=true)
     */
    private $createdDateTime;

    public function __construct()
    {
        $this->waterBodyDetails = new ArrayCollection();
        $this->applications = new ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return Lease
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getBnType() {
        
        $types = array(
            'WaterBody' => 'জল মহল',
            'Market' => 'হাট বাজার',
            'Gadget' => 'গ্যাজেট'
        );

        return isset($types[$this->type]) ? $types[$this->type] : "";
    }

    /**
     * Set shotangso
     *
     * @param string $shotangso
     * @return Lease
     */
    public function setShotangso($shotangso)
    {
        $this->shotangso = $shotangso;

        return $this;
    }

    /**
     * Get shotangso
     *
     * @return string 
     */
    public function getShotangso()
    {
        return $this->shotangso;
    }



    /**
     * @return mixed
     */
    public function getKhatian()
    {
        return $this->khatian;
    }

    /**
     * @param mixed $khatian
     */
    public function setKhatian($khatian)
    {
        $this->khatian = $khatian;
    }


    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param int $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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
    public function setGadget(Gadget $gadget)
    {
        $this->gadget = $gadget;
       // $gadget->addLease($this);
    }

    /**
     * @param mixed $waterBodyDetail
     */
    public function addWaterBodyDetail($waterBodyDetail)
    {
        if (!$this->waterBodyDetails->contains($waterBodyDetail)) {
            $this->waterBodyDetails->add($waterBodyDetail);
        }
    }

    /**
     * @param mixed $waterBodyDetail
     */
    public function removeWaterBodyDetail($waterBodyDetail)
    {
        if ($this->waterBodyDetails->contains($waterBodyDetail)) {
            $this->waterBodyDetails->removeElement($waterBodyDetail);
        }
    }

    /**
     * @param mixed $application
     */
    public function addApplication($application)
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
        }
    }

    /**
     * @param mixed $application
     */
    public function removeApplication($application)
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
        }
    }
    

    /**
     * @return Application[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplicationStatus()
    {
        foreach($this->getApplications() as $app){
            if($app->getStatus() != 'ARCHIVED'){
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getWaterBodyDetails()
    {
        return $this->waterBodyDetails;
    }

    /**
     * @param mixed $waterBodyDetails
     */
    public function setWaterBodyDetails($waterBodyDetails)
    {
        $this->waterBodyDetails = $waterBodyDetails;
    }

    /**
     * @return string
     */
    public function getFiscalyear()
    {
        return $this->fiscalyear;
    }

    /**
     * @param string $fiscalyear
     */
    public function setFiscalyear($fiscalyear)
    {
        $this->fiscalyear = $fiscalyear;
    }

    /**
     * @return mixed
     */
    public function getMarketDetail()
    {
        return $this->marketDetail;
    }

    /**
     * @param mixed $marketDetail
     */
    public function setMarketDetail($marketDetail)
    {
        $this->marketDetail = $marketDetail;
    }

    /**
     * @return mixed
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * @param mixed $histories
     */
    public function setHistories($histories)
    {
        $this->histories = $histories;
    }
    /**
     * Get BnStartDate
     *
     * @return \DateTime
     */
    public function getBnStartDate(){
        $bongabda = new BnDateTime($this->startDate->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Dhaka'));
        $bongabda->setMorning(0);
        //return $bongabda->format('l jS F Y b h:i:s');
        return $bongabda->format('jS F Y');
    }
    /**
     * Get BnEndDate
     *
     * @return \DateTime
     */
    public function getBnEndDate(){
        $bongabda = new BnDateTime($this->endDate->format('Y-m-d H:i:s'), new DateTimeZone('Asia/Dhaka'));
        $bongabda->setMorning(0);
        //return $bongabda->format('l jS F Y b h:i:s');
        return $bongabda->format('jS F Y');
    }

    public function checkRegisterForMarket(){
        $register =  $this->getApplication()->getRegisterSix()[0];

        return  count((array)$register);
    }

    public function checkRegisterForWater(){
        $registers =  $this->getApplication()->getRegisterSix();
        $amount = 0.0;
        foreach($registers as $register){
            $amount = $amount + $register->getChalanAmount();
        }

        $demandFee = $this->getApplication()->getTotalAmount();
        if(round($demandFee,2) == round($amount,2) and $demandFee != null){
            return  true;
        }else{
            return  false;
        }

    }

    /**
     * @return string
     */
    public function getTender()
    {
        return $this->tender;
    }

    /**
     * @param string $tender
     */
    public function setTender($tender)
    {
        $this->tender = $tender;
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
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}
