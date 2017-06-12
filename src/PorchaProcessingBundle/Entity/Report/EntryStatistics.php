<?php

namespace PorchaProcessingBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntryStatistics
 *
 * @ORM\Table(name="report_khatian_entry", indexes={@ORM\Index(name="report_date", columns={"report_date"})})
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\Report\EntryStatisticsRepository")
 */
class EntryStatistics
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
     * @var integer
     * @ORM\Column(name="entry", type="integer")
     */
    private $entry;

    /**
     * @var integer
     * @ORM\Column(name="entry_correction", type="integer")
     */
    private $entryCorrection;

    /**
     * @var integer
     * @ORM\Column(name="entry_total", type="integer")
     */
    private $entryTotal;

    /**
     * @var integer
     * @ORM\Column(name="entry_total_correction", type="integer")
     */
    private $entryTotalCorrection;

    /**
     * @var integer
     * @ORM\Column(name="verify", type="integer")
     */
    private $verify;

    /**
     * @var integer
     * @ORM\Column(name="verify_correction", type="integer")
     */
    private $verifyCorrection;

    /**
     * @var integer
     * @ORM\Column(name="verify_total", type="integer")
     */
    private $verifyTotal;

    /**
     * @var integer
     * @ORM\Column(name="verify_total_correction", type="integer")
     */
    private $verifyTotalCorrection;

    /**
     * @var integer
     * @ORM\Column(name="compare", type="integer")
     */
    private $compare;

    /**
     * @var integer
     * @ORM\Column(name="compare_correction", type="integer")
     */
    private $compareCorrection;

    /**
     * @var integer
     * @ORM\Column(name="compare_total", type="integer")
     */
    private $compareTotal;

    /**
     * @var integer
     * @ORM\Column(name="compare_total_correction", type="integer")
     */
    private $compareTotalCorrection;

    /**
     * @var integer
     * @ORM\Column(name="approve", type="integer")
     */
    private $approve;

    /**
     * @var integer
     * @ORM\Column(name="approve_correction", type="integer")
     */
    private $approveCorrection;

    /**
     * @var integer
     * @ORM\Column(name="approve_total", type="integer")
     */
    private $approveTotal;

    /**
     * @var integer
     * @ORM\Column(name="approve_total_correction", type="integer")
     */
    private $approveTotalCorrection;

    /**
     * @var integer
     * @ORM\Column(name="office_id", type="integer")
     */
    private $office;

    /**
     * @var \DateTime
     * @ORM\Column(name="report_date", type="date")
     */
    private $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param int $entry
     *
     * @return EntryStatistics
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * @return int
     */
    public function getEntryCorrection()
    {
        return $this->entryCorrection;
    }

    /**
     * @param int $entryCorrection
     *
     * @return EntryStatistics
     */
    public function setEntryCorrection($entryCorrection)
    {
        $this->entryCorrection = $entryCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getEntryTotal()
    {
        return $this->entryTotal;
    }

    /**
     * @param int $entryTotal
     *
     * @return EntryStatistics
     */
    public function setEntryTotal($entryTotal)
    {
        $this->entryTotal = $entryTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerify()
    {
        return $this->verify;
    }

    /**
     * @param int $verify
     *
     * @return EntryStatistics
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerifyCorrection()
    {
        return $this->verifyCorrection;
    }

    /**
     * @param int $verifyCorrection
     *
     * @return EntryStatistics
     */
    public function setVerifyCorrection($verifyCorrection)
    {
        $this->verifyCorrection = $verifyCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerifyTotal()
    {
        return $this->verifyTotal;
    }

    /**
     * @param int $verifyTotal
     *
     * @return EntryStatistics
     */
    public function setVerifyTotal($verifyTotal)
    {
        $this->verifyTotal = $verifyTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompare()
    {
        return $this->compare;
    }

    /**
     * @param int $compare
     *
     * @return EntryStatistics
     */
    public function setCompare($compare)
    {
        $this->compare = $compare;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompareCorrection()
    {
        return $this->compareCorrection;
    }

    /**
     * @param int $compareCorrection
     *
     * @return EntryStatistics
     */
    public function setCompareCorrection($compareCorrection)
    {
        $this->compareCorrection = $compareCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompareTotal()
    {
        return $this->compareTotal;
    }

    /**
     * @param int $compareTotal
     *
     * @return EntryStatistics
     */
    public function setCompareTotal($compareTotal)
    {
        $this->compareTotal = $compareTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getApprove()
    {
        return $this->approve;
    }

    /**
     * @param int $approve
     *
     * @return EntryStatistics
     */
    public function setApprove($approve)
    {
        $this->approve = $approve;

        return $this;
    }

    /**
     * @return int
     */
    public function getApproveTotal()
    {
        return $this->approveTotal;
    }

    /**
     * @param int $approveTotal
     *
     * @return EntryStatistics
     */
    public function setApproveTotal($approveTotal)
    {
        $this->approveTotal = $approveTotal;

        return $this;
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
     *
     * @return EntryStatistics
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return EntryStatistics
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getApproveCorrection()
    {
        return $this->approveCorrection;
    }

    /**
     * @param int $approveCorrection
     *
     * @return EntryStatistics
     */
    public function setApproveCorrection($approveCorrection)
    {
        $this->approveCorrection = $approveCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getEntryTotalCorrection()
    {
        return $this->entryTotalCorrection;
    }

    /**
     * @param int $entryTotalCorrection
     *
     * @return EntryStatistics
     */
    public function setEntryTotalCorrection($entryTotalCorrection)
    {
        $this->entryTotalCorrection = $entryTotalCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerifyTotalCorrection()
    {
        return $this->verifyTotalCorrection;
    }

    /**
     * @param int $verifyTotalCorrection
     *
     * @return EntryStatistics
     */
    public function setVerifyTotalCorrection($verifyTotalCorrection)
    {
        $this->verifyTotalCorrection = $verifyTotalCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompareTotalCorrection()
    {
        return $this->compareTotalCorrection;
    }

    /**
     * @param int $compareTotalCorrection
     *
     * @return EntryStatistics
     */
    public function setCompareTotalCorrection($compareTotalCorrection)
    {
        $this->compareTotalCorrection = $compareTotalCorrection;

        return $this;
    }

    /**
     * @return int
     */
    public function getApproveTotalCorrection()
    {
        return $this->approveTotalCorrection;
    }

    /**
     * @param int $approveTotalCorrection
     *
     * @return EntryStatistics
     */
    public function setApproveTotalCorrection($approveTotalCorrection)
    {
        $this->approveTotalCorrection = $approveTotalCorrection;

        return $this;
    }
}
