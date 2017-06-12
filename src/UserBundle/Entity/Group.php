<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user_groups")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\GroupRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="This group name is already in use."
 * )
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * Values (
     'MINISTRY',
     'DC',
     'UDC',
     'AC_LAND'
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $applicableTo;

    /**
     * Values (
    'MINISTRY',
    'DC',
    'UDC',
    'AC_LAND'
     * )
     * @ORM\Column(name="group_type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     **/
    protected $users;

    public function __construct($name, $roles = array())
    {
        parent::__construct($name, $roles);
        $this->users = new ArrayCollection();
    }

    /**
     * Set description
     *
     * @param mixed $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return mixed
     */
    public function getApplicableTo()
    {
        return $this->applicableTo;
    }

    /**
     * @param mixed $applicableTo
     *
     * @return Group
     */
    public function setApplicableTo($applicableTo)
    {
        $this->applicableTo = $applicableTo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

}