<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Model\CsvFileIterator;
use AppBundle\Entity\Office;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\JLNumber;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\NonDeliverableMessage;
use PorchaProcessingBundle\Entity\OfficeTemplate;
use PorchaProcessingBundle\Entity\Survey;
use PorchaProcessingBundle\Entity\Template;
use PorchaProcessingBundle\Entity\Thana;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\Group;
use UserBundle\Entity\Profile;
use UserBundle\Entity\User;

class LoadSetupData implements FixtureInterface, ContainerAwareInterface
{
    private  $users;

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ObjectManager
     */
    private $manager;

    private $rootDir;

    function __construct()
    {

    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        $this->rootDir = $this->container->getParameter('assetic.write_to');
    }

    /**
     * {@inheritDoc}
     */
    function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createDivisions();
        $this->createDistricts();
        //$this->createKhatianDistricts();

        $this->createUserGroups();
        $this->createUsers();
        $this->createOffices();
        $this->assignUsersToOffices();
        $this->createSurveys();

        //$this->createTemplates();
        //$this->createNonDeliverableMessages();
    }

    private function createSurveys() {

        $data = $this->surveyCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $survey = new Survey();
            $survey->setName($row[0]);
            $survey->setApproved($row[2]);
            $survey->setType($row[1]);
            $this->manager->persist($survey);
        }
        $this->manager->flush();
    }

    private function createOffices() {
        $ministry = null;
        $dc_office = null;
        $ac_land_office = null;
        $udc_office = null;

        $data = $this->officeCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $office = new Office();
            $office->setName($row[3]);
            $office->setType($row[0]);
            $office->setactive(1);
            $office->setRelatedDistricts(trim($row[1]));

            $district = $this->manager->getRepository('AppBundle:District')->findOneBy(array('geocode' => trim($row[1])));
            $office->setDistrict($district);

            $upozila = $this->manager->getRepository('AppBundle:Upozila')->findOneBy(array('name' => trim($row[2])));
            $office->setUpozila($upozila);

            /*if ($row[0] == 'MINISTRY') {
                $ministry = $office;
            } elseif ($row[0] == 'DC') {
                $dc_office = $office;
                $office->setParent($ministry);
            } elseif ($row[0] == 'AC_LAND') {
                $ac_land_office = $office;
                $office->setParent($dc_office);
            }*/

            $this->manager->persist($office);
        }
        $this->manager->flush();
    }

    private function assignUsersToOffices() {

        $data = $this->userCSV();
        foreach ($data as $row) {
            if (!empty($row[1])) {
                $district = $this->manager->getRepository('AppBundle:District')->findOneBy(array('geocode' => $row[1]));
                $office = $this->manager->getRepository('AppBundle:Office')->findOneBy(array('type' => 'DC', 'district' => $district));
                $user = $this->manager->getRepository('RbsUserBundle:User')->findOneBy(array('username' => $row[2]));

            } else {
                if (in_array($row[0], array('মন্ত্রণালয অ্যাডমিন়', 'মন্ত্রণালয পর্যবেক্ষক'))) {
                    $office = $this->manager->getRepository('AppBundle:Office')->findOneBy(array('type' => 'MINISTRY'));
                    $user = $this->manager->getRepository('RbsUserBundle:User')->findOneBy(array('username' => $row[2]));
                }
            }

            if (isset($user) && isset($office)) {
                $user->setOffice($office);
                $this->manager->persist($user);
            }
        }
        $this->manager->flush();
    }

    private function createUsers()
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $data = $this->userCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0 || !isset($row[5])) {$i++; continue;}
            $user = new User();
            $profile = new Profile();

            $profile->setUser($user);
            $profile->setFullNameEn($row[5]);

            $user->setUsername($row[2]);
            $user->setEmail($row[3]);
            $user->setPlainPassword($row[4]);
            $user->setEnabled(1);
            $user->setProfile($profile);

            $group = $this->manager->getRepository('RbsUserBundle:Group')->findOneBy(array('name' => $row[0]));
            if ($group) {
                $user->setGroups(array($group));
            }
            $userManager->updateUser($user);
        }
    }

    private function createUserGroups()
    {
        $data = $this->userGroupCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $permissions = array_filter(explode(',', $row[3]));
            $group = new Group($row[0], $permissions);
            $group->setApplicableTo($row[1]);
            $group->setType($row[2]);
            $group->setDescription($row[0]);
            $this->manager->persist($group);
        }
        $this->manager->flush();
    }

    private function createDistricts() {

        $data = $this->districtCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $district = new \AppBundle\Entity\District();
            $district->setName($row[0]);
            $district->setGeocode($row[1]);
            $district->setApproved(1);
            $this->manager->persist($district);
        }
        $this->manager->flush();
    }
    private function createDivisions() {

        $data = $this->divisionCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $division = new \AppBundle\Entity\Division();
            $division->setName($row[0]);
            $division->setGeocode($row[1]);

            $this->manager->persist($division);
        }
        $this->manager->flush();
    }

    private function createKhatianDistricts() {

        $data = $this->districtCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            $district = new District();
            $district->setName($row[0]);
            $district->setGeocode($row[1]);
            $district->setApproved(1);
            $this->manager->persist($district);
        }
        $this->manager->flush();
    }

    private function createTemplates()
    {
        $templates = array();
        $data = $this->templateCSV();
        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}
            list($survey, $name, $templateFile, $geocode) = $row;
            $survey = $this->manager->getRepository('PorchaProcessingBundle:Survey')->findOneBy(array('type' => $survey));

            $template = new Template();
            $template->setSurvey($survey);
            $template->setName($name);
            $template->setApproved(true);
            $template->setDeleted(false);
            $templateContent = file_get_contents($this->rootDir . '/csv/templates/' . $templateFile);
            $template->setBody($templateContent);
            $templates[] = $template;
            $this->manager->persist($template);
        }

        $offices = $this->manager->getRepository('AppBundle:Office')->findBy(array('type' => 'DC'));
        $ministryAdmin = $this->manager->getRepository('RbsUserBundle:User')->findOneBy(array('username' => 'MinAdmin'));
        foreach ($offices as $office) {
            foreach ($templates as $template) {
                $officeTemplate = new OfficeTemplate();
                $officeTemplate->setOffice($office);
                $officeTemplate->setTemplate($template);
                $officeTemplate->setSetBy($ministryAdmin);
                $this->manager->persist($officeTemplate);
            }
        }

        $this->manager->flush();
    }

    public function createNonDeliverableMessages() {

        $fileData = $this->nonDeliverableMessageCSV();

        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $ndm = new NonDeliverableMessage();
                $ndm->setMessage($row[0]);
                $ndm->setApproved(1);
                $this->manager->persist($ndm);
            }
        }
        $this->manager->flush();
    }

    private function nonDeliverableMessageCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/non_deliverable_messages.csv');
    }

    private function officeCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/offices.csv');
    }

    private function userCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/users.csv');
    }

    private function surveyCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/surveys.csv');
    }

    private function districtCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/districts.csv');
    }
    private function divisionCSV() {
            return new CsvFileIterator($this->rootDir . '/csv/divisions.csv');
        }

    private function userGroupCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/user_groups.csv');
    }

    private function templateCSV() {
        return new CsvFileIterator($this->rootDir . '/csv/templates.csv');
    }
}