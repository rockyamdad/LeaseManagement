<?php
namespace LeaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SMSSendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lease:sms-send')
            ->setDescription('Send SMS to all Leasee')
            ->setHelp("This command allows you to send SMS")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $applications = $this->getContainer()->get('doctrine')->getRepository('LeaseBundle:Application')->findBy(array('status'=>'APPROVED'));

        foreach($applications as $application){
            if($application->getLease()->getType() == 'WaterBody'){
                $currentDate = new \DateTime();

                if($currentDate->format('Y') == $application->getLease()->getEndDate()->format('Y')){
                    $smsText = 'Apnar Lease tir meyad sesh hote ar 30 din baki.';
                    $this->getContainer()->get('sms.transporter')->send($application->getPhoneNo(), $smsText);
                }

            }else{
                $application->setOtp(rand(0, 9999));
                $this->getContainer()->get('doctrine')->getRepository('LeaseBundle:Application')->create($application);

                $smsText = 'Apnar Lease tir meyad sesh hote ar matro 30 din baki . Punonibondhon korte chaile 15 diner vitor portale gie tracking Id('.$application->getApplicationTrackingId().') abong OTP NUmber('.$application->getOtp().') die Punonibondhon korun';
                $this->getContainer()->get('sms.transporter')->send($application->getPhoneNo(), $smsText);
            }
        }
        $text = 'SMS Send execute successfully';
        $output->writeln($text);

    }

}