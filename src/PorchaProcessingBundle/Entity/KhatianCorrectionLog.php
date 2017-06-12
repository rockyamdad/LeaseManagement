<?php

namespace PorchaProcessingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * KhaitanCorrectionLog
 *
 * @ORM\Table(name="khatian_correction_log")
 * @ORM\Entity
 */
class KhatianCorrectionLog
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
     * @ORM\OneToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianPage", inversedBy="correctionLog")
     * @ORM\JoinColumn(name="khatian_page_id", referencedColumnName="id")
     */
    private $khatianPage;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __toString()
    {
        return $this->getId(). "";
    }

    public function __construct($khatianPage, $user)
    {
        $this->khatianPage = $khatianPage;
        $this->user = $user;
        $this->createdAt = new \DateTime();
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
    public function getKhatianPage()
    {
        return $this->khatianPage;
    }

    /**
     * @param mixed $khatianPage
     *
     * @return KhaitanCorrectionLog
     */
    public function setKhatianPage($khatianPage)
    {
        $this->khatianPage = $khatianPage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param boolean $message
     *
     * @return KhaitanCorrectionLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return KhaitanCorrectionLog
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

}
