<?php

namespace PorchaProcessingBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * KhatianActivity
 *
 * @ORM\Table(name="khatian_activities")
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\KhatianActivityRepository")
 */
class KhatianActivity
{
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\Khatian")
     * @ORM\JoinColumn(name="khatian_id", referencedColumnName="id")
     */
    private $khatian;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="date_time", type="datetime")
     */
    private $dateTime;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }





}
