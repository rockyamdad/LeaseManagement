<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Office
 *
 * @ORM\Table(name="offices", indexes={@ORM\Index(name="office_type_idx", columns={"office_type"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfficeRepository")
 */
class Office
{
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\OfficeSettings", mappedBy="office", cascade={"persist"})
     */
    protected $officeSettings;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District", inversedBy="offices")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id", nullable=true)
     */
    protected $district;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Upozila")
     * @ORM\JoinColumn(name="upozila_id", referencedColumnName="id", nullable=true)
     */
    protected $upozila;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Union")
     * @ORM\JoinColumn(name="union_id", referencedColumnName="id", nullable=true)
     */
    protected $union;
    /**
     * @ORM\OneToMany(targetEntity="Office", mappedBy="parent")
     **/
    private $children;
    /**
     * @ORM\ManyToOne(targetEntity="Office", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     **/
    private $parent;
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=100, nullable=true)
     */
    private $address;
    /**
     * @var string
     * Values (
      'MINISTRY',
      'DC',
      'UDC',
      'AC_LAND'
     * )
     * @ORM\Column(name="office_type", type="string", length=50)
     */
    private $type;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;
    /**
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User", mappedBy="office")
     **/
    private $users;
    /**
     * @var string
     *
     * @ORM\Column(name="related_districts", type="string", length=100, nullable=true)
     */
    private $relatedDistricts;
    /**
     * @var string
     *
     * @ORM\Column(name="modules", type="json_array", nullable=true)
     */
    private $modules;
    /**
     * @var string
     *
     * @ORM\Column(name="office_services", type="json_array", nullable=true)
     */
    private $officeServices;
    /**
     * @var string
     *
     * @ORM\Column(name="application_receiving_mediums", type="json_array", nullable=true)
     */
    private $applicationReceivingMediums;
    /**
     * @var string
     *
     * @ORM\Column(name="contact_person", type="string", nullable=true)
     */
    private $contactPerson;
    /**
     * @var string
     *
     * @ORM\Column(name="record_room_in_charge_info", type="string", nullable=true)
     */
    private $recordRoomInChargeInfo;
    /**
     * @var string
     *
     * @ORM\Column(name="contact_info", type="string", nullable=true)
     */
    private $contactInfo;
    /**
     * @var string
     *
     * @ORM\Column(name="organization_id", type="string", nullable=true)
     */
    private $organizationId;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="string", nullable=true)
     */
    private $refId;
    /**
     * @var string
     *
     * @ORM\Column(name="ness_org_id", type="string", length=255, nullable=true)
     */
    private $nessOrgId;

    public function __construct()
    {
        $this->children = new ArrayCollection();

        if (!$this->getId()) {
            $this->applicationReceivingMediums = array();
            $this->modules = array();
            $this->officeServices = array();
        }
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
     * @return string
     */
    public function getNessOrgId()
    {
        return $this->nessOrgId;
    }

    /**
     * @param string $nessOrgId
     */
    public function setNessOrgId($nessOrgId)
    {
        $this->nessOrgId = $nessOrgId;
    }

    /**
     * @return mixed
     */
    public function getOfficeSettings()
    {
        return $this->officeSettings;
    }

    /**
     * @param mixed $officeSettings
     */
    public function setOfficeSettings($officeSettings)
    {
        $this->officeSettings = $officeSettings;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param string $contactPerson
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    }

    /**
     * @return string
     */
    public function getRecordRoomInChargeInfo()
    {
        return $this->recordRoomInChargeInfo;
    }

    /**
     * @param string $recordRoomInChargeInfo
     */
    public function setRecordRoomInChargeInfo($recordRoomInChargeInfo)
    {
        $this->recordRoomInChargeInfo = $recordRoomInChargeInfo;
    }

    /**
     * @return string
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }

    /**
     * @param string $contactInfo
     */
    public function setContactInfo($contactInfo)
    {
        $this->contactInfo = $contactInfo;
    }

    /**
     * @return string
     */
    public function getApplicationReceivingMediums()
    {
        return $this->applicationReceivingMediums;
    }

    /**
     * @param string $applicationReceivingMediums
     */
    public function setApplicationReceivingMediums($applicationReceivingMediums)
    {
        $this->applicationReceivingMediums = $applicationReceivingMediums;
    }

    /**
     * @return string
     */
    public function getOfficeServices()
    {
        return $this->officeServices;
    }

    /**
     * @param string $officeServices
     */
    public function setOfficeServices($officeServices)
    {
        $this->officeServices = $officeServices;
    }

    /**
     * @return string
     */
    public function getRelatedDistricts()
    {
        return $this->relatedDistricts;
    }

    /**
     * @param string $relatedDistricts
     */
    public function setRelatedDistricts($relatedDistricts)
    {
        $this->relatedDistricts = $relatedDistricts;
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
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return Upozila
     */
    public function getUpozila()
    {
        return $this->upozila;
    }

    /**
     * @param mixed $upozila
     */
    public function setUpozila($upozila)
    {
        $this->upozila = $upozila;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Office
     */
    public function setName($name)
    {
        $this->name = $name;

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

    /**
     * Set type
     *
     * @param string $type
     * @return Office
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Office
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Office
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     *
     * @return Office
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getOfficeTypeName($districtInclude = false)
    {
        $text = '';
        switch ($this->type) {
            case 'DC': $text .= 'DC Office'; break;
            case 'AC_LAND': $text .= 'AC Land Office'; break;
            case 'UDC': $text .= 'UDC Office'; break;
            default : $text = 'TYPE_MINISTRY';
        }

        if ($districtInclude) {
            $text .= ', ' . $this->getOfficeDistrictName();
        }

        return $text;
    }

    /**
     * @return mixed
     */
    public function getOfficeDistrictName()
    {
        return $this->district ? $this->district->getName() : '';
    }

    /**
     * @return string
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param string $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
    }

    /**
     * @return mixed
     */
    public function getUnion()
    {
        return $this->union;
    }

    /**
     * @param mixed $union
     */
    public function setUnion($union)
    {
        $this->union = $union;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param string $organizationId
     *
     * @return Office
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;

        return $this;
    }
}
