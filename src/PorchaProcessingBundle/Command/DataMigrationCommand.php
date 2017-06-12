<?php
namespace PorchaProcessingBundle\Command;

use PorchaProcessingBundle\Entity\VrrStatistics;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DataMigrationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('elrs:data-migrate')
            ->setDescription('elrs all data migration')

            ->setHelp(<<<EOT
            The <info>elrs:data-migrate</info> command data migrate:
            <info>php app/console elrs:data-migrate</info>
EOT
            );

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer()->get('porcha_processing.service.api_manager');
        $totalApplicationReceived = $container->updateTotalApplicationReceived();
        $totalApplicationDelivered = $container->updateTotalApplicationDelivered();
        $totalRecordDigitalized = $container->updateTotalRecordDigitalized();
        $totalRecordRoom = $container->updateTotalRecordRoom();
        $this->VrrStatisticsUpdated($totalApplicationReceived, $totalApplicationDelivered, $totalRecordDigitalized, $totalRecordRoom, $container);
        $text = 'data migrate execute successfully';
        $output->writeln($text);
    }

    /**
     * @param $totalApplicationReceived
     * @param $totalApplicationDelivered
     * @param $totalRecordDigitalized
     * @param $totalRecordRoom
     * @param $container
     */
    protected function VrrStatisticsUpdated($totalApplicationReceived, $totalApplicationDelivered, $totalRecordDigitalized, $totalRecordRoom, $container)
    {
        $vrrStats = new VrrStatistics();
        $vrrStats->setTotalAppReceived($totalApplicationReceived);
        $vrrStats->setTotalAppDelivered($totalApplicationDelivered);
        $vrrStats->setTotalDigitizedKhatian($totalRecordDigitalized);
        $vrrStats->setTotalRecordRoom($totalRecordRoom);
        $container->update($vrrStats);
    }
}