<?php

namespace UserBundle\Entity;

use AppBundle\Entity\Office;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already in use."
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This username is already in use."
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\WorkflowTeam", mappedBy="surveys", cascade={"persist", "remove"})
     **/
    protected $workflowTeams;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist"})
     */
    protected $profile;
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="join_users_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office", inversedBy="users")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id", nullable=true)
     **/
    private $office;
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Udc", mappedBy="user")
     */
    private $udc;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    private $officeAdmin = false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_ness_user", type="boolean", nullable=true)
     */
    private $isNessUser = false;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;

    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->workflowTeams = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getWorkflowTeams()
    {
        return $this->workflowTeams;
    }

    /**
     * @param mixed $workflowTeams
     */
    public function setWorkflowTeams($workflowTeams)
    {
        $this->workflowTeams = $workflowTeams;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     * @return $this
     */
    public function setProfile($profile)
    {
        $profile->setUser($this);

        $this->profile = $profile;

        return $this;
    }

    public function isSuperAdmin()
    {
        $groups = $this->getGroups();
        /** @var Group $group */
        foreach ($groups as $group) {
            if ($group->hasRole('ROLE_SUPER_ADMIN')) {
                return false;
            }
        }

        return parent::isSuperAdmin();
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $groups
     *
     * @return User
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;//is_array($groups) ? $groups : array($groups);

        return $this;
    }

    /**
     * @return Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param mixed $office
     *
     * @return User
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isOfficeAdmin()
    {
        return $this->officeAdmin;
    }

    /**
     * @param boolean $officeAdmin
     *
     * @return User
     */
    public function setOfficeAdmin($officeAdmin)
    {
        $this->officeAdmin = $officeAdmin;

        return $this;
    }

    public function getType()
    {
        return $this->getGroups()[0]->getType();
    }

    public function getNameWithUsername()
    {
        return  (($this->profile) ? $this->profile->getFullNameBn() : ''). ' (' .$this->getUsername(). ') ';
    }

    /**
     * @return mixed
     */
    public function getUdc()
    {
        return $this->udc;
    }

    /**
     * @param mixed $udc
     */
    public function setUdc($udc)
    {
        $this->udc = $udc;
    }

    /**
     * @return boolean
     */
    public function getIsNessUser()
    {
        return $this->isNessUser;
    }

    /**
     * @param boolean $isNessUser
     *
     * @return User
     */
    public function setIsNessUser($isNessUser)
    {
        $this->isNessUser = $isNessUser;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNessUser()
    {
        return $this->isNessUser;
    }

}