<?php

namespace UserBundle\Command;

use AppBundle\Entity\Office;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use FOS\UserBundle\Model\UserManager;
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

class NessUserImportCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    private $em;

    /** @var  NessApiManager */
    private $nessApiManager;

    /** @var UserManager */
    private $userManager;

    protected $groups = array();

    protected $users = array();

    protected function configure()
    {
        $this
            ->setName('user:ness-user:import')
            ->setDescription('Import Ness User')
            ->addArgument(
                'orgId',
                InputArgument::OPTIONAL,
                'Organization ID'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->nessApiManager = $this->getContainer()->get('ness.api_manager');
        $this->userManager = $this->getContainer()->get('fos_user.user_manager');
        $this->groups['DCAdmin'] = $this->em->getRepository('RbsUserBundle:Group')->findOneByName('ডিসি অ্যাডমিন');
        $this->groups['DCOfficer'] = $this->em->getRepository('RbsUserBundle:Group')->findOneByName('ডিসি অফিসার');
        $this->groups['MinistryAdmin'] = $this->em->getRepository('RbsUserBundle:Group')->findOneByName('মন্ত্রণালয অ্যাডমিন়');

        $param = array('type' => array('DC', 'MINISTRY'));
        if ($organizationId = $input->getArgument('orgId')) {
            $param['nessOrgId'] = $organizationId;
            //$param['id'] = 2;
        }

        $offices = $this->em->getRepository('AppBundle:Office')->findBy($param);
        
        /** @var Office $office */
        foreach ($offices as $office) {
            $this->disableAllNessUser($office->getId());

            if (!$office->getNessOrgId()) continue;

            $this->handleApiResponse($this->nessApiManager->getUserList($office->getNessOrgId()), $office);
            /*$this->handleApiResponse(
                file_get_contents(
                    realpath($this->getContainer()->get('kernel')->getRootDir().'/../web/').'/user_list.xml'
                ),
            $office);*/

            $output->writeln('<comment>'.$office->getName() .' - '. $office->getNessOrgId() .' - COMPLETED</comment>');
        }

        /*foreach ($this->users as $key => $user) {
            $count = count($user);
            if ($count > 1) {
                print_r($key . ' => ' . json_encode($user) . PHP_EOL);
            }
        }*/

        $output->writeln('<info>FINISH</info>');
    }

    private function handleApiResponse($content, $office)
    {
        if (!$content) {
            return;
        }

        $data = simplexml_load_string($content);

        foreach ($data->orgdata as $row) {
            /*$r = $this->extractValue($row);
            $this->users[$r['username']][] = $r['email'];
            $this->users[$r['email']][] = $r['username'];*/
            $this->persistUser($this->extractValue($row), $office);
        }
    }

    function extractValue($row)
    {
        return $this->nessApiManager->extractUserData($row);
    }

    protected function persistUser($row, Office $office)
    {
        $mode = 'update';
        if (!$user = $this->userManager->findUserByUsername(trim($row['username']))) {
            $mode = 'create';
            $user = new User();
            $profile = new Profile();
            $profile->setUser($user);
            $profile->setDesignation($row['designation']);

            $user->setProfile($profile);
            $user->setPlainPassword(uniqid(time()));
            $user->setUsername($row['username']);
            $user->setEmail($row['username'] . '-' . $row['email']);
            $user->setIsNessUser(true);

            if (trim($row['designation']) == 'জেলা প্রশাসক') {
                $user->addGroup($this->groups['DCAdmin']);
            } else {
                if (trim($row['designation']) == 'SENIOR ASSISTANT CHIEF') {
                    $user->addGroup($this->groups['MinistryAdmin']);
                } else {
                    $user->addGroup($this->groups['DCOfficer']);
                }
            }
        }

        if ($row['designation'] != $user->getProfile()->getDesignation()) {
            print_r('DUPLICATE Designation ' . $row['email'] . ' -> '.$row['username'] . PHP_EOL);
            return false;
        }

        if ($mode == 'create' && $this->userManager->findUserByEmail(trim($row['email']))) {
            print_r('Exists ' . $row['email'] . ' -> '.$row['username'] . PHP_EOL);
            return false;
        }

        if (!$user->isNessUser()) {
            print_r('Not Ness User ' . $user->getUsername() . PHP_EOL);
            return false;
        }

        $user->setEnabled(true);
        $user->setOffice($office);

        $user->getProfile()->setFullNameEn($row['full_name_en']);
        $user->getProfile()->setFullNameBn($row['full_name_bn']);
        $user->getProfile()->setFathersFullNameEn($row['fathers_full_name_en']);
        $user->getProfile()->setFathersFullNameBn($row['fathers_full_name_bn']);
        $user->getProfile()->setMothersFullNameEn($row['mothers_full_name_en']);
        $user->getProfile()->setMothersFullNameBn($row['mothers_full_name_bn']);
        $user->getProfile()->setGender($row['gender'] == 'পুরুষ' ? 'MALE' : 'FEMALE');
        $user->getProfile()->setNid($row['nid']);
        $user->getProfile()->setCellphone($row['cellphone']);
        $user->getProfile()->setDob(!empty($row['dob']) ? new \DateTime($row['dob']) : null);
        $user->getProfile()->setPhoto($row['photo']);
        $user->getProfile()->setSignature($row['signature']);
        
        $this->userManager->updateUser($user);
        unset($user);

        return true;
    }

    protected function disableAllNessUser($officeId)
    {
        $this->em->createQuery(
            "UPDATE RbsUserBundle:User u SET u.enabled = 0 WHERE u.isNessUser = 1 and u.office = $officeId"
        )->execute();
    }

    protected function isUsernameOrEmailExists($row)
    {
        $qb = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('RbsUserBundle:User')->createQueryBuilder('u');

        return $qb->select('COUNT(u.id) AS found')
            ->where($qb->expr()->eq('u.username', ':username'))
            ->setParameter('username', $row['username'])
            //->orWhere($qb->expr()->eq('u.email', ':email'))
            //->setParameter('email', $row['username'].'-'.$row['username'])
            ->getQuery()->getSingleScalarResult();
    }

}
