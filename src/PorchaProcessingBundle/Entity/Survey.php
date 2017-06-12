<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 *
 * @ORM\Table(name="surveys")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\WorkflowTeam", mappedBy="surveys", cascade={"persist", "remove"})
     **/
    protected $workflowTeams;

    public function __construct()
    {
        $this->workflowTeams = new ArrayCollection();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var array $type
     * Values (
    'CS',
    'SA',
    'RS',
    'BS',
    'DIARA'
     * )
     * @ORM\Column(name="survey_type", type="string", length=15)
     */
    private $type;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

    public function __toString()
    {
        return $this->getName();
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
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }


}
