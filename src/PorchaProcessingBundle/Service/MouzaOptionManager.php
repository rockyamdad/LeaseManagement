<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Traits\EntityAssistant;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Document;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\JLNumber;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Thana;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MouzaOptionManager
{
    use QueryAssistant;
    use EntityAssistant;

    protected $em;
    protected $office;
    protected $user;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }

    public function getDistrictsArray($criteria) {
        $rows = $this->em->getRepository('PorchaProcessingBundle:District')->findBy(array_merge($criteria, array('deleted' => 0)));
        $ret = array();
        if ($rows) {
            foreach ($rows as $row) {
                $ret[$row->getGeocode()] = $row->getName();
            }
        }
        return $ret;
    }

    public function getSurveys($criteria = array()) {
        return $this->em->getRepository('PorchaProcessingBundle:Survey')->findBy(array_merge($criteria, array('approved' => 1)));
    }

    public function getDistricts($criteria = array()) {
        return $this->em->getRepository('PorchaProcessingBundle:District')->findBy(array_merge($criteria, array('deleted' => 0)));
    }

    public function getJlnumberByMouza($mouzaId, $surveyType) {

        $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->find($mouzaId);
        return $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
            'mouza' => $mouza,
            'surveyType' => $surveyType,
            'approved' => 1
        ));
    }

    public function getRelatedDistricts() {

        $relatedDistricts = $this->office->getRelatedDistricts();

        $qb = $this->em->getRepository('PorchaProcessingBundle:District')->createQueryBuilder('d');
        $qb->where('d.approved = 1');
        $qb->andWhere('d.deleted = 0');

        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andwhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        return $qb->getQuery()->getResult();
    }
    public function getRelatedUpozilas() {

        $relatedDistricts = $this->office->getRelatedDistricts();

        $qb = $this->em->getRepository('PorchaProcessingBundle:District')->createQueryBuilder('d');
        $qb->where('d.approved = 1');
        $qb->andWhere('d.deleted = 0');

        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andwhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        $districts =  $qb->getQuery()->getResult();

        $districtId = array();
        foreach($districts as $district){
            $districtId[] = $district->getId();
        }

        $qb = $this->em->getRepository('PorchaProcessingBundle:Upozila')->createQueryBuilder('u');
        $qb->where('u.district IN (:districts)');
        $qb->setParameter('districts', $districtId);
        $result = $qb->getQuery()->getResult();
        return $result;

    }

    public function getRelatedDistrictIds() {

        $relatedDistricts = $this->office->getRelatedDistricts();

        $qb = $this->em->getRepository('PorchaProcessingBundle:District')->createQueryBuilder('d');
        $qb->where('d.approved = 1');
        $qb->andWhere('d.deleted = 0');

        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andwhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        $arrDistrictId = array();
        $rows = $qb->getQuery()->getResult();
        if ($rows) {
            foreach ($rows as $row) {
                $arrDistrictId[] = $row->getId();
            }
        }

        return $arrDistrictId;
    }

    public function getThanas($criteria = array()) {
        return $this->em->getRepository('PorchaProcessingBundle:Thana')->findBy(array_merge($criteria, array('deleted' => 0)));
    }

    public function getThanasByUserDistrict($districtId, $criteria = array()) {
        $district = $this->em->getRepository('PorchaProcessingBundle:District')->find($districtId);
        return $this->em->getRepository('PorchaProcessingBundle:Thana')->findBy(
            array_merge($criteria, array(
                'deleted' => 0,
                'district' => $district
            )));
    }

    public function getParganas($criteria = array()) {
        return $this->em->getRepository('PorchaProcessingBundle:Pargana')->findBy(array_merge($criteria, array('deleted' => 0)));
    }

    public function getMouzas($criteria = array()) {
        return $this->em->getRepository('PorchaProcessingBundle:Mouza')->findBy(array_merge($criteria, array('deleted' => 0)));
    }

    public function getMouzaList($data, $filterBy)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Mouza')->createQueryBuilder('m');
        $qb->join('m.upozila', 'u');
        $qb->join('u.district', 'd');
        $qb->where('m.deleted != 1');

            if (isset($data['nameSearch']) && !empty($data['nameSearch'])) {
                $qb->andWhere($qb->expr()->like('u.name', ':name'));
                $qb->setParameter('name', $data['nameSearch'].'%');
            }


        $relatedDistricts = $this->office->getRelatedDistricts();
        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andWhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('d.geocode', 'asc');
        }

        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

    public function getJlnumberList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->createQueryBuilder('j');
        $qb->join('j.mouza', 'm');
//        $qb->leftJoin('m.thana', 't');
//        $qb->leftJoin('m.upozila', 'u');
        $qb->where('m.deleted = 0');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('j.name', 'asc');
        }

        return $qb->getQuery()->getResult();
    }

    public function createMouza($requestAll) {

        $data = $requestAll['porcha_processing_mouza'];

        $upozila = $this->em->getRepository('PorchaProcessingBundle:Upozila')->find($data['upozila']);

        $arrParam['name'] = $data['name'];
        $arrParam['upozila'] = $upozila;
        $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findOneBy($arrParam);

        if (!$mouza) {

            $mouza = new Mouza();
            $mouza->setName($data['name']);
            if ((isset($data['approved']))) {
                $mouza->setApproved(true);
            }
            $mouza->setUpozila($upozila);
            $this->em->persist($mouza);
            $this->em->flush();

            $types = array(
                'CS' => array($this->convertNumber('bn2en', $data['csJLNumber']), $data['csDistrict'], $data['csThana'], $data['csMouzaName'], $data['csDivision']),
                'SA' => array($this->convertNumber('bn2en', $data['saJLNumber']), $data['saDistrict'], $data['saThana'], $data['saMouzaName'], $data['saDivision']),
                'RS' => array($this->convertNumber('bn2en', $data['rsJLNumber']), $data['rsDistrict'], $data['rsThana'], $data['rsMouzaName'], $data['rsDivision']),
                'BS' => array($this->convertNumber('bn2en', $data['bsJLNumber']), $data['bsDistrict'], $data['bsThana'], $data['bsMouzaName'], $data['bsDivision']),
                'PETY' => array($this->convertNumber('bn2en', $data['petyJLNumber']), $data['petyDistrict'], $data['petyThana'], $data['petyMouzaName'], $data['petyDivision']),
                'DIARA' => array($this->convertNumber('bn2en', $data['diaraJLNumber']), $data['diaraDistrict'], $data['diaraThana'], $data['diaraMouzaName'], $data['diaraDivision']),
            );

            foreach ($types as $type=>$value) {

                if (empty($value[0])) {
                    continue;
                }

                $jlNumber = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
                    'surveyType' => $type,
                    'mouza' => $mouza,
                    'name' => $value[0]
                ));

                if (!$jlNumber) {
                    $jlNumber = new JLNumber();
                    $jlNumber->setSurveyType($type);
                    $jlNumber->setMouza($mouza);
                    $jlNumber->setApproved(1);
                }

                $jlNumber->setName($value[0]);
                $jlNumber->setDistrict($value[1]);
                $jlNumber->setThana($value[2]);
                $jlNumber->setMouzaName($value[3]);
                $jlNumber->setDivision($value[4]);

                $this->em->persist($jlNumber);
                $this->em->flush();
            }
        }

        return $mouza;
    }

    public function getMouzaJlnumberInfo($mouza) {

        $ret = array();
        $jlnumbers = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findBy(array('mouza' => $mouza));
        if ($jlnumbers) {
            foreach ($jlnumbers as $jlnumber) {
                $ret[strtoupper($jlnumber->getSurveyType())] = array(
                    'id' => $jlnumber->getId(),
                    'name' => $jlnumber->getName(),
                    'district' => $jlnumber->getDistrict(),
                    'thana' => $jlnumber->getThana(),
                    'mouzaName' => $jlnumber->getMouzaName(),
                    'division' => $jlnumber->getDivision(),
                );
            }
        }
        return $ret;
    }

    public function updateMouza(Mouza $mouza, $requestAll) {

        $data = $requestAll['porcha_processing_mouza'];

        $mouza->setName($data['name']);
        $this->em->persist($mouza);
        $this->em->flush();

        $types = array(
            'CS' => array($data['csJLNumberId'], $this->convertNumber('bn2en', $data['csJLNumber']), $data['csDistrict'], $data['csThana'], $data['csMouzaName'], $data['csDivision']),
            'SA' => array($data['saJLNumberId'], $this->convertNumber('bn2en', $data['saJLNumber']), $data['saDistrict'], $data['saThana'], $data['saMouzaName'], $data['saDivision']),
            'RS' => array($data['rsJLNumberId'], $this->convertNumber('bn2en', $data['rsJLNumber']), $data['rsDistrict'], $data['rsThana'], $data['rsMouzaName'], $data['rsDivision']),
            'BS' => array($data['bsJLNumberId'], $this->convertNumber('bn2en', $data['bsJLNumber']), $data['bsDistrict'], $data['bsThana'], $data['bsMouzaName'], $data['bsDivision']),
            'PETY' => array($data['petyJLNumberId'], $this->convertNumber('bn2en', $data['petyJLNumber']), $data['petyDistrict'], $data['petyThana'], $data['petyMouzaName'], $data['petyDivision']),
            'DIARA' => array($data['diaraJLNumberId'], $this->convertNumber('bn2en', $data['diaraJLNumber']), $data['diaraDistrict'], $data['diaraThana'], $data['diaraMouzaName'], $data['diaraDivision']),
        );

        foreach ($types as $type=>$value) {

            if ($value[0] != 0) {
                $jlNumber = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->find($value[0]);
            } else {
                if ($value[1] == '') {
                    continue;
                }
                $jlNumber = new JLNumber();
                $jlNumber->setSurveyType($type);
                $jlNumber->setMouza($mouza);
                $jlNumber->setApproved(1);
            }

            $jlNumber->setName($value[1]);
            $jlNumber->setDistrict($value[2]);
            $jlNumber->setThana($value[3]);
            $jlNumber->setMouzaName($value[4]);
            $jlNumber->setDivision($value[5]);
            $this->em->persist($jlNumber);
            $this->em->flush();
        }

        return $mouza;
    }


    public function updateJlnumber(JLNumber $jlnumber) {

        $this->em->persist($jlnumber);
        $this->em->flush();
    }

    public function getDistrictList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:District')->createQueryBuilder('d');
        $qb->where('d.deleted = 0');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getUpozilaList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Upozila')->createQueryBuilder('u');
        $qb->join('u.district', 'd');
        $qb->where('u.deleted = 0');

        $relatedDistricts = $this->office->getRelatedDistricts();
        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andWhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }


    public function updateDistrict($formData, District $district) {
        $data = $formData['form'];
        $district->setName($data['name']);
        if (isset($data['deleted'])) {
            $district->setDeleted($data['deleted']);
        }
        $this->em->persist($district);
        $this->em->flush();
    }

    public function importDistricts(Document $document) {
        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());

        unset($fileData[0]);
        foreach ($fileData as $row) {
            if (!empty($row[0])) {
                $this->insertDistrictIfNotExists($row[0]);
            }
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

    private function insertDistrictIfNotExists($districtName) {
        $district = $this->em->getRepository('PorchaProcessingBundle:District')->findBy(array('name' => $districtName));
        if (!$district) {
            $district = new District();
            $district->setName($districtName);
            $district->setDeleted(false);
            $this->em->persist($district);
            $this->em->flush();
        }
    }

    public function updateUpozila($formData, Upozila $upozila) {
        $data = $formData['form'];
        $district = $this->em->getRepository('PorchaProcessingBundle:District')->find($data['district']);

        $upozila->setName($data['name']);
        $upozila->setDistrict($district);
        if (isset($data['deleted'])) {
            $upozila->setDeleted($data['deleted']);
        }
        $this->em->persist($upozila);
        $this->em->flush();
    }

    public function getThanaList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('PorchaProcessingBundle:Thana')->createQueryBuilder('t');
        $qb->join('t.district', 'd');
        $qb->where('t.deleted = 0');

        $relatedDistricts = $this->office->getRelatedDistricts();
        if (!empty($relatedDistricts)) {
            $relatedDistricts = explode(",", $relatedDistricts);
            $qb->andWhere('d.geocode IN (:districtGeocode)');
            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('t.name', 'asc');
        }
        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function importThanas(Document $document) {
        $document->upload();
        $this->em->persist($document);
        $this->em->flush();

        $fileData = $this->getCSVFileData($document->getWebPath());

        unset($fileData[0]);
        foreach ($fileData as $row) {
            list($district, $name) = $row;

            if (!empty($name)) {
                $thana = $this->em->getRepository('PorchaProcessingBundle:Thana')->findOneBy(array('name' => $name));
                if (!$thana) {

                    $district = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('name' => $district));

                    $thana = new Thana();
                    $thana->setName($name);
                    $thana->setDistrict($district);
                    $this->em->persist($thana);
                }
            }
        }
        $this->em->flush();
    }

    public function update($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function getMouzaPastInfo($surveyType, $mouzaId)
    {
        $ret = array('district' => '', 'thana' => '');
        $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->find($mouzaId);

        if ($mouza) {
            $row = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array(
                'surveyType' => $surveyType,
                'mouza' => $mouza
            ));
            $ret['district'] = $row->getDistrict();
            $ret['thana'] = $row->getThana();
        }
        return $ret;
    }
}