<?php

namespace PorchaProcessingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkflowTeam
 *
 * @ORM\Table(name="workflow_team")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\WorkflowTeamRepository")
 */
class WorkflowTeam
{
    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\Survey", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_surveys",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="survey_id", referencedColumnName="id")}
     * )
     */
    protected $surveys;
    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\Upozila", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_upozilas",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="upozila_id", referencedColumnName="id")}
     * )
     */
    protected $upozilas;
    /**
     * @ORM\ManyToMany(targetEntity="PorchaProcessingBundle\Entity\Mouza", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_mouzas",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mouza_id", referencedColumnName="id")}
     * )
     */
    protected $mouzas;
    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_eoperators",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entry_opetator_id", referencedColumnName="id")}
     * )
     */
    protected $entryOperators;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office", referencedColumnName="id")
     */
    private $office;
    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_verifiers",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="verifier_id", referencedColumnName="id")}
     * )
     */
    private $verifiers;
    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_comparers",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="comparer_id", referencedColumnName="id")}
     * )
     */
    private $comparers;
    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="workflowTeams")
     * @ORM\JoinTable(name="join_workflow_teams_approvers",
     *      joinColumns={@ORM\JoinColumn(name="workflow_team_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="approver_id", referencedColumnName="id")}
     * )
     */
    private $approvers;
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;
    /**
     * @var array $type
     * Values (
    'BATCH',
    'APP',
     * )
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @return mixed
     */
    public function getEntryOperators()
    {
        return $this->entryOperators;
    }

    /**
     * @param mixed $entryOperators
     */
    public function setEntryOperators($entryOperators)
    {
        $this->entryOperators = $entryOperators;
    }

    /**
     * @return mixed
     */
    public function getVerifiers()
    {
        return $this->verifiers;
    }

    /**
     * @param mixed $verifiers
     */
    public function setVerifiers($verifiers)
    {
        $this->verifiers = $verifiers;
    }

    /**
     * @return mixed
     */
    public function getComparers()
    {
        return $this->comparers;
    }

    /**
     * @param mixed $comparers
     */
    public function setComparers($comparers)
    {
        $this->comparers = $comparers;
    }

    /**
     * @return mixed
     */
    public function getApprovers()
    {
        return $this->approvers;
    }

    /**
     * @param mixed $approvers
     */
    public function setApprovers($approvers)
    {
        $this->approvers = $approvers;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $type
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
     * @return mixed
     */
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     * @param mixed $surveys
     */
    public function setSurveys($surveys)
    {
        $this->surveys = $surveys;
    }

    /**
     * @return mixed
     */
    public function getUpozilas()
    {
        return $this->upozilas;
    }

    /**
     * @param mixed $upozilas
     */
    public function setUpozilas($upozilas)
    {
        $this->upozilas = $upozilas;
    }

    /**
     * @return mixed
     */
    public function getMouzas()
    {
        return $this->mouzas;
    }

    /**
     * @param mixed $mouzas
     */
    public function setMouzas($mouzas)
    {
        $this->mouzas = $mouzas;
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
}
