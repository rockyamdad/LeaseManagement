<?php

namespace LeaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentSchedule
 *
 * @ORM\Table(name="payment_schedules")
 * @ORM\Entity(repositoryClass="LeaseBundle\Repository\PaymentScheduleRepository")
 */
class PaymentSchedule
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
     * @var \DateTime
     *
     * @ORM\Column(name="paymentDate", type="datetime",nullable=true)
     */
    private $paymentDate;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float",nullable=true)
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\Application",inversedBy="schedule")
     * @ORM\JoinColumn(name="application_id",referencedColumnName="id",nullable=true)
     */
    private $applications;

    /**
     * @ORM\ManyToOne(targetEntity="LeaseBundle\Entity\RegisterLeaseSix",inversedBy="schedule")
     * @ORM\JoinColumn(name="register_six_id",referencedColumnName="id",nullable=true)
     */
    private $registerSixes;


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
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     * @return PaymentSchedule
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime 
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return PaymentSchedule
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param mixed $applications
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return mixed
     */
    public function getRegisterSixes()
    {
        return $this->registerSixes;
    }

    /**
     * @param mixed $registerSixes
     */
    public function setRegisterSixes($registerSixes)
    {
        $this->registerSixes = $registerSixes;
    }
}
