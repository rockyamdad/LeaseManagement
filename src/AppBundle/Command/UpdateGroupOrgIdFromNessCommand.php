<?php

namespace AppBundle\Command;

use AppBundle\Entity\Office;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use FOS\UserBundle\Model\UserManager;
use PorchaProcessingBundle\EventListener\CountItem;
use PorchaProcessingBundle\Service\DashboardManager;
use PorchaProcessingBundle\Service\KhatianManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\Group;
use UserBundle\Entity\Profile;
use UserBundle\Entity\User;
use UserBundle\Service\NessApiManager;

class UpdateGroupOrgIdFromNessCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    private $em;

    /** @var DashboardManager */
    private $dashboardManager;

    /** @var KhatianManager */
    private $khatianManager;

    /** @var  NessApiManager */
    private $nessApiManager;

    /** @var UserManager */
    private $userManager;

    protected $groups = array();

    protected function configure()
    {
        $this
            ->setName('elrs:update-office-org-from-ness')
            ->setDescription('Update Org ID from NESS');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->nessApiManager = $this->getContainer()->get('ness.api_manager');
        $nessOffices = $this->nessApiManager->getOfficeList();
        $offices = $this->prepareApiResponseData($nessOffices);

        $this->disableEventListener();

        foreach($offices as $geoCode => $orgId) {
            if ($office = $this->getOfficeByGeocode($geoCode)) {
                $output->writeln("<info>{$geoCode} => {$orgId} => {$office->getId()}</info>");
                $office->setNessOrgId($orgId);
                $this->em->flush();
            } else {
                //print_r($geoCode . " => " . $orgId . " => NOT FOUND " . PHP_EOL);
            }
        }

        if ($ministryOffice = $this->em->getRepository('AppBundle:Office')->findOneByType('MINISTRY')) {
            $ministryOffice->setNessOrgId('ORG131000030026000000');
        }
        
        $this->em->flush();

        $output->writeln('<info>====== FINISH ======</info>');
    }

    private function handleApiResponse($content, $office)
    {
        if (!$content) {
            return;
        }

        $data = simplexml_load_string($content);

        foreach ($data->orgdata as $row) {
            $this->persistUser($this->extractValue($row), $office);
        }
    }

    private function prepareApiResponseData($xmlData)
    {
        $output = array();
        $xml = simplexml_load_string($xmlData);
        foreach ($xml as $r) {

            if ((string)$r->tbl11_org_type != 4) continue;

            $output[(string)$r->tbl11_org_district] = (string)$r->tbl11_org_id;
        }

        return $output;
    }


    /**
     * @param $geoCode
     * @return Office
     */
    private function getOfficeByGeocode($geoCode)
    {
        $qb = $this->em->getRepository('AppBundle:Office')->createQueryBuilder('o');
        $qb->join('o.district', 'd');
        $qb->where('d.geocode = :geoCode')->setParameter('geoCode', $geoCode);
        $qb->andWhere('o.type = :type')->setParameter('type', 'DC');
        $result = $qb->getQuery()->getResult();

        if ($result) {
            return $result[0];
        }

        return null;
    }

    private function disableEventListener()
    {
        $listenerInst = null;
        $em = $this->em;
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof CountItem) {
                    $listenerInst = $listener;
                    break 2;
                }
            }
        }

        if ($listenerInst) {
            $evm = $em->getEventManager();
            $evm->removeEventListener(array('postUpdate', 'prePersist'), $listenerInst);
        }
    }
}
