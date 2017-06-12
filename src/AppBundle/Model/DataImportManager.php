<?php
namespace AppBundle\Model;

use AppBundle\Entity\Division;
use AppBundle\Entity\Document;
use AppBundle\Entity\Union;
use AppBundle\Traits\EntityAssistant;
use AppBundle\Traits\QueryAssistant;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Exception;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\JLNumber;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\NonDeliverableMessage;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataImportManager
{
    use QueryAssistant;
    use EntityAssistant;

    protected $em;
    private $container;
    private $rootDir;

    private $districts = array();
    private $upozilas = array();
    private $mouzas = array();
    private $jlnumbers = array();


    /** @var resource */
    private $conn;

    public function __construct(EntityManager $entityManager, ContainerInterface $container = null) {

        $this->em = $entityManager;

        $this->container = $container;
        $this->rootDir = $this->container->getParameter('assetic.write_to');
        $this->conn = $this->em->getConnection()->getWrappedConnection()->getConnectionResource();
        //$this->connectionInit();
    }

    private function connectionInit() {

        $config = new \Doctrine\DBAL\Configuration();

        $params = array(
            'dbname' => $this->container->getParameter('database_name'),
            'user' => $this->container->getParameter('database_user'),
            'password' => $this->container->getParameter('database_password'),
            'host' => $this->container->getParameter('database_host'),
            'port' => $this->container->getParameter('database_port'),
            'charset' => 'utf8',
            'driver' => 'pdo_mysql',
        );

        $this->conn = DriverManager::getConnection($params, $config);
    }

    private function populateExistingData() {

        $select = "khatian_upozilas.geocode as uGeocode, khatian_upozilas.id as kuId, ";
        $select .= "mouzas.name as mName, mouzas.id as mId, ";
        $select .= "jlnumbers.survey_type, jlnumbers.name as jName ";

        $sql = "SELECT $select FROM jlnumbers ";
        $sql .= " INNER JOIN mouzas ON mouzas.id = jlnumbers.mouza_id ";
        $sql .= " INNER JOIN khatian_upozilas ON khatian_upozilas.id = mouzas.upozila_id ";


        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            $this->mouzas[$row['UGEOCODE']][$row['MNAME']] = $row['MID'];
            $this->jlnumbers[$row['MID']][$row['SURVEY_TYPE']][$row['JNAME']] = $row['JNAME'];
        }
    }

    private function createDistrictIfNotExists($districtGeocode, $districtName) {

        $districtId = $this->checkIfDistrictExists($districtGeocode);
        if ($districtId) {
            return $districtId;
        }

        $row = array(
            'name' => $districtName,
            'geocode' => $districtGeocode,
            'division' => $districtGeocode,
            'deleted' => 0,
            'approved' => 1
        );

        $nextId = $this->getNextGeneratedId('khatian_districts');
        $row['ID'] = $nextId;
        $this->insert('khatian_districts', $row);
        $row['id'] = $nextId;

        $this->districts[$districtGeocode] = $row['id'];
        return $row['id'];
    }

    private function checkIfDistrictExists($districtGeocode) {

        $sql = "SELECT * FROM (SELECT id FROM khatian_districts WHERE geocode = '$districtGeocode') WHERE ROWNUM = 1";
        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            return $row['ID'];
        }

        return 0;
    }

    private function createUpozilaIfNotExists($districtId, $districtGeocode, $upozilaGeocode, $upozilaName) {

        $upozilaId = $this->checkIfUpozilaExists($districtId, $upozilaGeocode);
        if ($upozilaId) {
            return $upozilaId;
        }

        $nextId = $this->getNextGeneratedId('khatian_upozilas');
        $this->insert('khatian_upozilas', array(
            'id' => $nextId,
            'district_id' => $districtId,
            'name' => $upozilaName,
            'geocode' => $upozilaGeocode,
            'deleted' => 0,
            'approved' => 1
        ));

        return $this->upozilas[$districtGeocode][$upozilaGeocode] = $nextId;
    }

    private function checkIfUpozilaExists($districtId, $upozilaGeocode) {

        $sql = "SELECT * FROM (SELECT id FROM khatian_upozilas WHERE district_id = '$districtId' AND geocode = '$upozilaGeocode') WHERE ROWNUM = 1";

        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            return $row['ID'];
        }
        return false;
    }

    private function createMouzaIfNotExists($upozilaId, $upozilaGeocode, $mouzaName) {

        $mouzaId = $this->checkIfMouzaExists($upozilaId, $mouzaName);
        if ($mouzaId) {
            return $mouzaId;
        }

        $id = $this->getNextGeneratedId('mouzas');
        $data = array(
            'id' => $id,
            'upozila_id' => $upozilaId,
            'name' => $mouzaName,
            'deleted' => 0,
            'approved' => 1
        );
        $this->insert('mouzas', $data);

        return $this->mouzas[$upozilaGeocode][$mouzaName] = $id;
    }

    private function checkIfMouzaExists($upozilaId, $mouzaName) {

        $sql = "SELECT * FROM (SELECT id FROM mouzas WHERE upozila_id = '$upozilaId' AND name = '$mouzaName') WHERE ROWNUM = 1";
        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            return $row['ID'];
        }

        return false;
    }

    private function createUpdateJLNumber($mouzaId, $jlInfo) {

        foreach ($jlInfo as $surveyType => $info) {

            $data = array(
                'mouza_id' => $mouzaId,
                'survey_type' => strtoupper($surveyType),
                'name' => $info[0],
                'district' => $info[1],
                'thana' => $info[2],
                'mouza_name' => $info[3],
                'division' => $info[4],
                'deleted' => 0,
                'approved' => 1
            );

            if (isset($this->jlnumbers[$mouzaId][$surveyType][$info[0]])) {
                $this->update("jlnumbers", $data, array('name' => $info[0], 'survey_type' => strtoupper($surveyType), 'mouza_id' => $mouzaId));
            } else {

                $data['ID'] = $this->getNextGeneratedId('jlnumbers');
                $this->insert('jlnumbers', $data);
            }
        }
    }

    public function importMouzas(Document $document)
    {
        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $this->populateExistingData();

        $data = $this->getCSVFileData($document->getWebPath());
        $i = 0;
        $j = 1;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}

            $districtId = $this->createDistrictIfNotExists($this->convertNumber('bn2en', $row[0]),$row[0]);

            $upozilaId = $this->createUpozilaIfNotExists($districtId, $this->convertNumber('bn2en', $row[0]), $this->convertNumber('bn2en', $row[1]), $row[1]);

            $mouzaId = $this->createMouzaIfNotExists($upozilaId, $row[1], $row[2]);

            $jlInfo = array(
                'CS' => array($this->convertNumber('bn2en', $row[3]), $row[4], $row[5], $row[6], $row[7]),
                'SA' => array($this->convertNumber('bn2en', $row[8]), $row[9], $row[10], $row[11], $row[12]),
                'RS' => array($this->convertNumber('bn2en', $row[13]), $row[14], $row[15], $row[16], $row[17]),
            );
            $this->createUpdateJLNumber($mouzaId, $jlInfo);
            $j++;
        }

        return new Response('success');
    }

    public function importDistricts(Document $document, $app = false) {

        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());
//var_dump($fileData);die;
        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $this->createUpdateDistricts($app, $row[1], $row[0], $row[2]);
            }
        }
    }
    public function importDivisions(Document $document, $app = false) {

        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());

        unset($fileData[0]);

        foreach ($fileData as $row) {

            if (!empty($row[0])) {
                $this->createUpdateDivisions($app, $row[1], $row[0]);
            }
        }
    }

    private function createUpdateDistricts($app, $geocode, $districtName, $divisionGeocode) {

        $division = $this->em->getRepository('AppBundle:Division')->findOneBy(array('geocode' => $divisionGeocode));
        if ($app) {
            $district = $this->em->getRepository('AppBundle:District')->findOneBy(array('geocode' => $geocode));
            if (!$district) {
                $district = new \AppBundle\Entity\District();
            }
        } else {
            $district = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('geocode' => $geocode));
            if (!$district) {
                $district = new District();
            }
        }

        if ($division) {
            $district->setDivision($division);
        }
        $district->setGeocode($geocode);
        $district->setName($districtName);
        $district->setApproved(true);
        $district->setDeleted(false);
        $this->em->persist($district);
        $this->em->flush();
    }
    private function createUpdateDivisions($app, $geocode, $divisionName) {

        $division = $this->em->getRepository('AppBundle:Division')->findOneBy(array('geocode' => $geocode));
        if(!$division){
            $division = new Division();
            $division->setGeocode($geocode);
            $division->setName($divisionName);
            $this->em->persist($division);
             $this->em->flush();
            return $division;
        }
    }

    public function importUpozilas(Document $document, $app = false) {

        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());

        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $this->createUpdateUpozilas($app, $row[0], $row[1], $row[2]);
            }
        }
    }
    public function importUnions(Document $document, $app = false) {

        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());

        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $this->createUpdateUnions($row[0], $row[1], $row[2], $row[3]);
            }
        }
    }

    private function createUpdateUpozilas($app, $districtGeocode, $upozilaName, $upozilaGeocode) {

        if ($app) {
            $district = $this->em->getRepository('AppBundle:District')->findOneBy(array('geocode' => $districtGeocode));
            $upozila = $this->em->getRepository('AppBundle:Upozila')->findOneBy(array('geocode' => $upozilaGeocode, 'district' => $district));

            if (!$upozila) {
                $upozila = new \AppBundle\Entity\Upozila();
            }
        } else {
            $district = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('geocode' => $districtGeocode));
            $upozila = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array('geocode' => $upozilaGeocode, 'district' => $district));

            if (!$upozila) {
                $upozila = new Upozila();
            }
        }

        $upozila->setName($upozilaName);
        $upozila->setGeocode($upozilaGeocode);
        $upozila->setDistrict($district);
        $upozila->setApproved(true);
        $upozila->setDeleted(false);
        $this->em->persist($upozila);
        $this->em->flush();
    }
    private function createUpdateUnions($districtGeocode, $upozilaGeocode, $unionName, $unionGeocode) {

            $district = $this->em->getRepository('AppBundle:District')->findOneBy(array('geocode' => $districtGeocode));

            $upozila = $this->em->getRepository('AppBundle:Upozila')->findOneBy(array('geocode' => $upozilaGeocode, 'district' => $district));


        $union = $this->em->getRepository('AppBundle:Union')->findOneBy(array('geocode' => $unionGeocode, 'upozila'=>$upozila));

        if(!$union){

            $union = new Union();
            $union->setName($unionName);
            $union->setGeocode($unionGeocode);
            $union->setUpozila($upozila);
            $union->setApproved(true);
            $union->setDeleted(false);
            $this->em->persist($union);
            $this->em->flush();
        }
    }

    public function getCSVFileData($webPath) {
        $fileData = array();
        $file = fopen($webPath,"r");
        while(! feof($file)) {
            $fileData[] = fgetcsv($file, 1024);
        }
        fclose($file);
        return $fileData;
    }

    public function nonDeliverableMessageImport() {

        $fileData = $this->getCSVFileData($this->rootDir . '/csv/non_deliverable_messages.csv');

        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $this->createNonDeliverableMessage($row[0]);
            }
        }
    }

    public function createNonDeliverableMessage($message) {

        $ndm = new NonDeliverableMessage();
        $ndm->setMessage($message);
        $ndm->setApproved(1);
        $this->em->persist($ndm);
        $this->em->flush();
    }

    protected function insert($table, $data)
    {
        #$seq = strtoupper($table).'_ID_SEQ.NEXTVAL';
        #$data['ID'] = $seq;

        $column = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$column})  VALUES ({$values})";

        // Replace SEQ
        #$sql = str_replace("'".$seq."'", $seq, $sql);

        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);
    }

    protected function update($table, $data, $where)
    {
        #$seq = strtoupper($table).'_ID_SEQ.NEXTVAL';
        #$data['ID'] = $seq;

        $updateFields = array();
        $whereFields = array();

        foreach ($data as $column => $value) {
            $updateFields[] = $column . " = '" . $value . "'";
        }
        $update = 'SET ' . implode(',', $updateFields);

        foreach ($where as $column => $value) {
            $whereFields[] = $column . " = '" . $value . "'";
        }
        $where = 'WHERE ' . implode(' AND ', $whereFields);

        $sql = "UPDATE {$table} {$update} {$where}";

        // Replace SEQ
        #$sql = str_replace("'".$seq."'", $seq, $sql);

        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);
    }

    protected function getNextGeneratedId($table)
    {
        $sql = 'SELECT ' . strtoupper($table) . '_ID_SEQ.NEXTVAL FROM DUAL';
        $stid = oci_parse($this->conn, $sql);
        oci_execute($stid);

        while (($row = oci_fetch_assoc($stid)) != false) {
            return $row['NEXTVAL'];
        }
    }

    public function importDataIntoDivisionDistrictUpozillaUnions(Document $document, $app = false) {

            //  $fileData = $this->getCSVFileData($document);
            $document->upload();
            $this->em->persist($document);
            $this->em->flush();

            $fileData = $this->getCSVFileData($document->getWebPath());

            unset($fileData[0]);

            $lists = array();
            $data = array();
            foreach ($fileData as $row) {
                if (!empty($row[0])) {

                    $centerCode = $this->centerCodeExplode($row);
                    $v = trim($row[6]).'-'.trim($centerCode).'-'.trim($row[10]);

                    //for user create
                    $lists['email'] = $v;
                    $lists['username'] = $v;
                    $lists['password'] = '123456';
                    $lists['mobileNumber'] = $row[12];
                    $lists['gender'] = $row[13];
                    $lists['address'] = $row[11];
                    $lists['fullName'] = $row[9];

                    // for office create
                    $lists['districtGeocode'] = $row[2];
                    $lists['upozilaGeocode'] = $row[4];
                    $lists['unionGeocode'] = $row[6];
                    $lists['officeName']=$row[8];


                    $data[] = $lists;
                }
            }

            return $data;

    }

    public function centerCodeExplode($row){

        $explode = explode('-',$row[8]);
        $end = '';

        if(count($explode) > 0){
            $end = array_pop($explode);
        }

       return $end;
    }

    private function createUpdateDivisionsDistrictsUpozilasUnions($app, $geocode, $districtName, $divisionGeocode) {

        $division = $this->em->getRepository('AppBundle:Division')->findOneBy(array('geocode' => $divisionGeocode));
        if ($app) {
            $district = $this->em->getRepository('AppBundle:District')->findOneBy(array('geocode' => $geocode));
            if (!$district) {
                $district = new \AppBundle\Entity\District();
            }
        } else {
            $district = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('geocode' => $geocode));
            if (!$district) {
                $district = new District();
            }
        }

        if ($division) {
            $district->setDivision($division);
        }
        $district->setGeocode($geocode);
        $district->setName($districtName);
        $district->setApproved(true);
        $district->setDeleted(false);
        $this->em->persist($district);
        $this->em->flush();
    }
}