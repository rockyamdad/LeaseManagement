<?php

namespace PorchaProcessingBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use PorchaProcessingBundle\Service\DashboardManager;
use PorchaProcessingBundle\Service\KhatianManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KhatianEntryStatisticsCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    private $em;

    /** @var DashboardManager */
    private $dashboardManager;

    /** @var KhatianManager */
    private $khatianManager;

    protected function configure()
    {
        $this
            ->setName('khatian:entry-statistics')
            ->setDescription('Added Khatian Entry Statistics')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Report Date',
                date('Y-m-d')
            )->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'load all data'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        //$this->dashboardManager = $this->getContainer()->get('porcha_processing.service.dashboard_manager');
        //$this->khatianManager = $this->getContainer()->get('porcha_processing.service.khatian_manager');

        $date = $input->getArgument('date');
        if ($input->getOption('all')) {
            $output->writeln('<comment>Start loading all khatian entry statistics data</comment>');

            $this->loadAllData($output);
        } else {
            $output->writeln('<comment>Start loading all khatian entry statistics data for ' . $date . '</comment>');

            $this->loadForDate($date);
        }

        $output->writeln('<info>=============== DONE ===============</info>');
    }

    private function loadForDate($date, $removeExist = true)
    {
        $this->em->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->updateForDate($date, $removeExist);
    }

    private function loadAllData(OutputInterface $output)
    {
        $sql = "TRUNCATE TABLE REPORT_KHATIAN_ENTRY";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();

        $totalDates = $this->getDates();
        $totalRecord = count($totalDates);
        foreach ($totalDates AS $key => $row) {
            $this->loadForDate($row['ENTRY_DATE'], false);
            $date = date('Y-m-d H:i:s');
            $complete = $key + 1;
            $output->writeln("<comment>{$date} - Record Of {$row['ENTRY_DATE']} - {$complete} of {$totalRecord} Complete</comment>");
        }
    }

    private function getDates()
    {
        $sql = "SELECT DISTINCT TO_CHAR(TRUNC(ENTRY_AT), 'YYYY-MM-DD') AS entry_date FROM KHATIAN_LOGS WHERE ENTRY_AT IS NOT NULL ORDER BY TO_CHAR(TRUNC(ENTRY_AT), 'YYYY-MM-DD')";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
