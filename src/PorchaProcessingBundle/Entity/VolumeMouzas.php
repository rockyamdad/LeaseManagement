<?php

namespace PorchaProcessingBundle\Entity;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * VolumeMouzas
 *
 * @ORM\Table(name="volume_mouzas")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\VolumeMouzasRepository")
 */
class VolumeMouzas
{

    use EntityAssistant;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Volume", cascade={"persist"})
     * @ORM\JoinColumn(name="volume_id", referencedColumnName="id")
     */
    private $volume;

    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Mouza")
     * @ORM\JoinColumn(name="mouza_id", referencedColumnName="id")
     */
    private $mouza;
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
     * @ORM\Column(name="start_khatian_no", type="integer")
     */
    private $startKhatianNo;
    /**
     * @var integer
     *
     * @ORM\Column(name="end_khatian_no", type="integer")
     */
    private $endKhatianNo;

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
    public function getMouza()
    {
        return $this->mouza;
    }

    /**
     * @param mixed $mouza
     */
    public function setMouza($mouza)
    {
        $this->mouza = $mouza;
    }

    /**
     * @return int
     */
    public function getStartKhatianNo()
    {
        return $this->startKhatianNo;
    }

    /**
     * @param int $startKhatianNo
     */
    public function setStartKhatianNo($startKhatianNo)
    {
        $this->startKhatianNo = $this->convertNumber('bn2en', $startKhatianNo);
    }

    /**
     * @return int
     */
    public function getEndKhatianNo()
    {
        return $this->endKhatianNo;
    }

    /**
     * @param int $endKhatianNo
     */
    public function setEndKhatianNo($endKhatianNo)
    {
        $this->endKhatianNo = $this->convertNumber('bn2en', $endKhatianNo);
    }
}
