<?php

namespace PorchaProcessingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Template
 *
 * @ORM\Table(name="office_templates")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\OfficeTemplateRepository")
 */
class OfficeTemplate
{

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;
    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="set_by", referencedColumnName="id")
     */
    private $setBy;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getSetBy()
    {
        return $this->setBy;
    }

    /**
     * @param mixed $setBy
     */
    public function setSetBy($setBy)
    {
        $this->setBy = $setBy;
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
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



}
