<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VolumeTemplate
 *
 * @ORM\Table(name="volume_templates")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\VolumeTemplateRepository")
 */
class VolumeTemplate
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
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Volume", inversedBy="volumeTemplates")
     * @ORM\JoinColumn(name="volume_id", referencedColumnName="id")
     */
    private $volume;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_page1_id", referencedColumnName="id")
     */
    private $templatePage1;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_page1_additional_id", referencedColumnName="id", nullable=true)
     */
    private $templatePage1Additional;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_page2_id", referencedColumnName="id", nullable=true)
     */
    private $templatePage2;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_page2_additional_id", referencedColumnName="id", nullable=true)
     */
    private $templatePage2Additional;

    /**
     * @var array $type
     *
     * @ORM\Column(name="volume_type", type="string", length=255)
     */
    private $type;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return mixed
     */
    public function getTemplatePage1()
    {
        return $this->templatePage1;
    }

    /**
     * @param mixed $templatePage1
     */
    public function setTemplatePage1($templatePage1)
    {
        $this->templatePage1 = $templatePage1;
    }

    /**
     * @return mixed
     */
    public function getTemplatePage1Additional()
    {
        return $this->templatePage1Additional;
    }

    /**
     * @param mixed $templatePage1Additional
     */
    public function setTemplatePage1Additional($templatePage1Additional)
    {
        $this->templatePage1Additional = $templatePage1Additional;
    }

    /**
     * @return mixed
     */
    public function getTemplatePage2()
    {
        return $this->templatePage2;
    }

    /**
     * @param mixed $templatePage2
     */
    public function setTemplatePage2($templatePage2)
    {
        $this->templatePage2 = $templatePage2;
    }

    /**
     * @return mixed
     */
    public function getTemplatePage2Additional()
    {
        return $this->templatePage2Additional;
    }

    /**
     * @param mixed $templatePage2Additional
     */
    public function setTemplatePage2Additional($templatePage2Additional)
    {
        $this->templatePage2Additional = $templatePage2Additional;
    }
}