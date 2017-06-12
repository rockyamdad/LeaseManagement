<?php
namespace PorchaProcessingBundle\Service;

use AppBundle\Entity\Office;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Entity\Upozila;
use PorchaProcessingBundle\Entity\VrrStatistics;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\Valid;

class ApiManager
{
    use QueryAssistant;
    protected $em;

    public function __construct(EntityManager $entityManager) {

        $this->em = $entityManager;
    }

    public function getSurveys() {

        $surveys = $this->em->getRepository('PorchaProcessingBundle:Survey')->findAll();

        $ret = array();
        foreach ($surveys as $survey) {
            $ret[] = array(
                'id' => $survey->getId(),
                'name' => $survey->getName(),
                'surveyType' => $survey->getType(),
                'approved' => ($survey->isApproved()) ? 1 : 0
            );
        }

        return $ret;
    }
    public function getUdcList() {

        $UdcLists = $this->em->getRepository('AppBundle:Office')->findBy(array('type' => 'UDC'));

        $ret = array();
        foreach ($UdcLists as $UdcList) {
            $ret[] = array(
                'id' => $UdcList->getId(),
                'name' => $UdcList->getName(),
                'address' => $UdcList->getAddress(),
                'contactInfo' => $UdcList->getContactInfo(),
                'approved' => ($UdcList->isActive()) ? 1 : 0
            );
        }

        return $ret;
    }

    public function getDivisions() {

        $divisions = $this->em->getRepository('AppBundle:Division')->findAll();

        $ret = array();
        foreach ($divisions as $division) {
            $ret[] = array(
                'name' => $division->getName(),
                'geoCode' => $division->getGeocode(),
                'id' => $division->getId(),
                'approved' => ($division->isApproved()) ? 1 : 0
            );
        }

        return $ret;
    }

    public function getAllDistricts() {

        $districts = $this->em->getRepository('AppBundle:District')->findAll();

        $ret = array();
        foreach ($districts as $district) {
            $ret[] = array(
                'name' => $district->getName(),
                'geoCode' => $district->getGeocode(),
                'id' => $district->getId(),
                'approved' => ($district->isApproved()) ? 1 : 0
            );
        }

        return $ret;
    }

    public function getDistricts($division) {

        $districts = $this->em->getRepository('PorchaProcessingBundle:District')->findBy(array('division' => $division,  'approved' => 1));

        $totalUdcServiceDelivered='';
        $totalServiceDelivered='';
        $totalRecordDigitization='';
        $totalApplicationReceived='';

        $ret = array();
        foreach ($districts as $district) {

            $totalUdcServiceDelivered +=$district->getUdcServiceDelivered();
            $totalServiceDelivered +=$district->getTotalServiceDelivered();
            $totalApplicationReceived +=$district->getTotalApplicationReceived();
            $totalRecordDigitization +=$district->getTotalKhatianCS()+$district->getTotalKhatianSA()+$district->getTotalKhatianRS();

            $ret[] = array(
                'id' => $district->getId(),
                'name' => $district->getName(),
                'divisionName' => $district->getDivision()->getName(),
                'geoCode' => $district->getGeocode(),
                'divisionGeocode' => $district->getDivision()->getGeocode(),
                'approved' => (int)$district->isApproved(),
                'totalUpozila' => $district->getTotalUpozila(),
                'totalMouza' => $district->getTotalMouza(),
                'totalKhatianCS' => $district->getTotalKhatianCS(),
                'totalKhatianSA' => $district->getTotalKhatianSA(),
                'totalKhatianRS' => $district->getTotalKhatianRS(),
                'totalUdc' => $district->getTotalUDC(),
                'totalKhatian' => $district->getTotalKhatianRS() + $district->getTotalKhatianCS() + $district->getTotalKhatianSA(),
                'totalKhatianReceived' => $district->getTotalKhatianAppReceived(),
                'totalKhatianDelivered' => $district->getTotalKhatianAppDelivered(),
                'totalOtherAppDelivered' => $district->getTotalOthersAppDelivered(),
                'totalOtherAppReceived' => $district->getTotalOthersAppReceived(),
                'totalServiceDelivered' => $district->getTotalServiceDelivered(),
                'totalApplicationReceived' => $district->getTotalApplicationReceived(),
                'UdcServiceDelivered' => $district->getUdcServiceDelivered(),

            );
        }

        return array('data'=>$ret,'totalServiceDelivered'=>$totalServiceDelivered,'totalRecordDigitization'=>$totalRecordDigitization,'totalApplicationReceived'=>$totalApplicationReceived,'totalUdcServiceDelivered'=>$totalUdcServiceDelivered);
    }

    public function getPorchaList($user) {

        $porchaLists = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getPorchAppliedList($user);
        $ret = array();

        foreach ($porchaLists as $porchaList) {

            $ret[] = array(
                'status' =>$porchaList->getServiceRequest()->getStatus(),
                'createdAt' =>$porchaList->getServiceRequest()->getCreatedAt(),
                'estimateDeliveryAt' =>$porchaList->getServiceRequest()->getEstimateDeliveryAt(),
                'surveyType' => $this->em->getRepository('PorchaProcessingBundle:Survey')->find($porchaList->getSurvey())->getName(),
                'upozila' => $this->em->getRepository('PorchaProcessingBundle:Upozila')->find($porchaList->getUpozila())->getName(),
                'mouza' => $this->em->getRepository('PorchaProcessingBundle:Mouza')->find($porchaList->getMouza())->getName(),
                'khatianNo' => $porchaList->getKhatianNo(),
                'khatianId' => ($porchaList->getKhatianLog()) ? $porchaList->getKhatianLog()->getKhatianVersion()->getKhatian()->getId() : 0,
                'applicationNo' => $porchaList->getServiceRequest()->getId(),

            );

        }
        return $ret;
    }

    public function getMouzaMapList($user) {

        $mouzaMapLists = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getMouzaAppliedList($user);
        $ret = array();

        foreach ($mouzaMapLists as $mouzaMapList) {
            $ret[] = array(
                'status' =>$mouzaMapList->getServiceRequest()->getStatus(),
                'createdAt' =>$mouzaMapList->getServiceRequest()->getCreatedAt(),
                'estimateDeliveryAt' =>$mouzaMapList->getServiceRequest()->getEstimateDeliveryAt(),
                'surveyType' => $this->em->getRepository('PorchaProcessingBundle:Survey')->find($mouzaMapList->getSurvey())->getName(),
                'upozila' => $this->em->getRepository('PorchaProcessingBundle:Upozila')->find($mouzaMapList->getUpozila())->getName(),
                'mouza' => $this->em->getRepository('PorchaProcessingBundle:Mouza')->find($mouzaMapList->getMouza())->getName(),
                'dagNo' => $mouzaMapList->getDagNo(),
                'sheetNo' => $mouzaMapList->getSheetNo(),
                'applicationNo' => $mouzaMapList->getServiceRequest()->getId(),
            );
        }
        return $ret;
    }

    public function getCaseCopyList($user) {

        $caseCopyLists = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestCaseCopy')->getCaseCopyAppliedList($user);
        $ret = array();

        foreach ($caseCopyLists as $caseCopyList) {
            $ret[] = array(
                'status' =>$caseCopyList->getServiceRequest()->getStatus(),
                'createdAt' =>$caseCopyList->getServiceRequest()->getCreatedAt(),
                'estimateDeliveryAt' =>$caseCopyList->getServiceRequest()->getEstimateDeliveryAt(),
                'caseNo' => $caseCopyList->getCaseNo(),
                'courtName' => $caseCopyList->getCourtName(),
                'lawyerName' => $caseCopyList->getLawyerName(),
                'plaintiffDefendant' => $caseCopyList->getPlaintiffDefendant(),
                'applicationNo' => $caseCopyList->getServiceRequest()->getId(),
            );
        }
        return $ret;
    }

    public function getInformationSlipList($user) {

        $informationSlipLists = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->findBy(array('contactNumber'=>$user,'type'=>'INFORMATION_SLIP'));
        $ret = array();

        foreach ($informationSlipLists as $informationSlipList) {
            $ret[] = array(
                'status' =>$informationSlipList->getStatus(),
                'createdAt' =>$informationSlipList->getCreatedAt(),
                'estimateDeliveryAt' =>$informationSlipList->getEstimateDeliveryAt(),
                'applicationNo' => $informationSlipList->getId(),
            );
        }
        return $ret;
    }

    public function getUpozilas($district) {

        $upozilas = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findBy(array('district'=>$district,'approved' => 1));
        $districtObject=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('id'=>$district));
        $totalRecordDigitization='';
        $ret = array();
        foreach ($upozilas as $upozila) {
            $totalRecordDigitization +=$upozila->getTotalKhatianCS()+$upozila->getTotalKhatianSA()+$upozila->getTotalKhatianRS();
            $ret[] = array(
                'id' => $upozila->getId(),
                'name' => $upozila->getName(),
                'divisionName' => $upozila->getDistrict()->getDivision()->getName(),
                'districtName' => $upozila->getDistrict()->getName(),
                'geoCode' => $upozila->getGeocode(),
                'districtGeocode' => $upozila->getDistrict()->getGeocode(),
                'approved' => ($upozila->isApproved()) ? 1 : 0,
                'totalMouza' => $upozila->getTotalMouza(),
                'totalKhatianCS' => $upozila->getTotalKhatianCS(),
                'totalKhatianSA' => $upozila->getTotalKhatianSA(),
                'totalKhatianRS' => $upozila->getTotalKhatianRS(),
                'totalUdc' => $upozila->getTotalUDC(),
                'totalKhatian' => $upozila->getTotalKhatianRS() + $upozila->getTotalKhatianCS() + $upozila->getTotalKhatianSA(),
                'totalKhatianReceived' => $upozila->getTotalKhatianAppReceived(),
                'totalKhatianDelivered' => $upozila->getTotalKhatianAppDelivered(),
                'totalOtherAppDelivered' => $upozila->getTotalOthersAppDelivered(),
                'totalOtherAppReceived' => $upozila->getTotalOthersAppReceived(),
            );
        }

        return array('data'=>$ret,'totalRecordDigitization'=>$totalRecordDigitization,'totalUdcServiceDelivered'=>$districtObject->getUdcServiceDelivered(),'totalServiceDelivered'=>$districtObject->getTotalServiceDelivered(),'totalApplicationReceived'=>$districtObject->getTotalApplicationReceived());
    }

    public function getMouzas($upozila) {

        $mouzas = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('upozila'=>$upozila,'approved' => 1));
        $upozilaObject = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array('id'=>$upozila));
        $ret = array();
        $totalServiceDelivered='';
        $totalRecordDigitization='';
        $totalApplicationReceived='';
        foreach ($mouzas as $mouza) {
            $totalServiceDelivered +=$mouza->getTotalServiceDelivered();
            $totalApplicationReceived +=$mouza->getTotalKhatianAppReceived()+$mouza->getTotalOthersAppReceived();
            $totalRecordDigitization +=$mouza->getTotalKhatianCS()+$mouza->getTotalKhatianSA()+$mouza->getTotalKhatianRS();
            $ret[] = array(
                'id' => $mouza->getId(),
                'name' => $mouza->getName(),
                'divisionName' => $mouza->getUpozila()->getDistrict()->getDivision()->getName(),
                'districtName' => $mouza->getUpozila()->getDistrict()->getName(),
                'upozilaName' =>  $mouza->getUpozila()->getName(),
                'upozilaGeocode' => $mouza->getUpozila()->getGeocode(),
                'approved' => ($mouza->isApproved()) ? 1 : 0,
                'totalKhatianCS' => $mouza->getTotalKhatianCS(),
                'totalKhatianSA' => $mouza->getTotalKhatianSA(),
                'totalKhatianRS' => $mouza->getTotalKhatianRS(),
                'totalKhatian' => $mouza->getTotalKhatianRS() + $mouza->getTotalKhatianCS() + $mouza->getTotalKhatianSA(),
                'totalKhatianReceived' => $mouza->getTotalKhatianAppReceived(),
                'totalKhatianDelivered' => $mouza->getTotalKhatianAppDelivered(),
                'totalOtherAppDelivered' => $mouza->getTotalOthersAppDelivered(),
                'totalOtherAppReceived' => $mouza->getTotalOthersAppReceived(),
            );
        }

        return array('data'=>$ret,'totalServiceDelivered'=>$totalServiceDelivered,'totalRecordDigitization'=>$totalRecordDigitization,'totalApplicationReceived'=>$totalApplicationReceived,'totalUdcServiceDelivered'=>$upozilaObject->getUdcServiceDelivered());
    }
    public function getVolumes($mouza,$type) {


        $volumes = $this->em->getRepository('PorchaProcessingBundle:Khatian')->getVolumes($mouza,$type);
        return $volumes;

    }

    public function getJlnumbers() {

        $jlnumbers = $this->em->getRepository('PorchaProcessingBundle:JLNumber')->findBy(array('approved' => 1));

        $ret = array();
        foreach ($jlnumbers as $jlnumber) {
            $ret[] = array(
                'name' => $jlnumber->getName(),
                'surveyType' => $jlnumber->getSurveyType(),
                'mouzaName' => $jlnumber->getMouza()->getName(),
                'district' => $jlnumber->getDistrict(),
                'thana' => $jlnumber->getThana(),
                'approved' => ($jlnumber->isApproved()) ? 1 : 0
            );
        }

        return $ret;
    }

    public function handleMouzaCreate($mouza){

        $this->updateMouzaCountForDistrict($mouza->getUpozila()->getDistrict());
        $this->updateMouzaCountForUpozila($mouza->getUpozila());

    }

    public function handleUpozilaCreate($upozila){

        $this->updateUpozilaCountForDistrict($upozila->getDistrict());
    }

    public function handleAllApplicationCreate(ServiceRequest $service)
    {
        $this->updateDistrictCountForApplication($service->getOffice());
        $this->handleTotalUDCServiceDelivered($service->getOffice()->getDistrict());
        $this->handleTotalServiceDelivered($service->getOffice()->getDistrict());

        $this->updateDistrictForApplication($service,$service->getOffice()->getDistrict());

        if (in_array($service->getType(), array('PORCHA_REQUEST', 'MOUZA_MAP'))) {
            $porcha = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findOneBy(array('serviceRequest' => $service));
            if ($porcha) {
                $this->handlePorchaMouzaApplicationCreate($porcha);
            }
        }
    }

    public function handlePorchaMouzaApplicationCreate(ServiceRequestPorcha $porcha)
    {
        $service = $porcha->getServiceRequest();

        $this->updateUpozilaForApplication($service, $porcha->getUpozila());

        $this->updateMouzaForApplication($service, $porcha->getMouza());
    }

    public function handleUdcCreate($office){

        $this->updateUdcForDistrict($office->getDistrict());

        $this->updateUdcForUpozila($office->getUpozila());

    }
    public  function updateUdcForDistrict($district){

        $totalUdc = $this->em->getRepository('AppBundle:Office')->getTotalUdc($district);
        $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
            'id'=> $district
        ));

        if($district){
            $district->setTotalUDC($totalUdc[0]['total']);
            $this->update($district);
        }

    }
    public  function updateUdcForUpozila($upozila){

        $totalUdc = $this->em->getRepository('AppBundle:Office')->getTotalUdcForUpozila($upozila);
        $upozila = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array(
            'id'=> $upozila
        ));

        if($upozila){
            $upozila->setTotalUDC($totalUdc[0]['total']);
            $this->update($upozila);
        }

    }


    public function handleKhatianCreate(Khatian $khatian) {

        $this->updateKhatianCountForDistrict($khatian->getVolume()->getDistrict(),$khatian->getVolume()->getSurvey()->getType());

        $this->updateKhatianCountForUpozila($khatian->getVolume()->getUpozila(),$khatian->getVolume()->getSurvey()->getType());

        $this->updateKhatianCountForMouza($khatian->getMouza(),$khatian->getVolume()->getSurvey()->getType());

    }

    public function updateUpozilaCountForDistrict(District $district){

        $totalUpozila = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalUpozila($district);

        $district->setTotalUpozila($totalUpozila);
        $this->update($district);

    }

    public function updateDistrictCountForApplication(Office $office){

        $totalApplicationByDistrict=$this->em->getRepository('PorchaProcessingBundle:District')->getTotalReceivedApplication($office);

        $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
            'id'=> $office->getDistrict(),
        ));

        $district->setTotalApplicationReceived($totalApplicationByDistrict);
        $this->update($district);

    }

    public function updateMouzaCountForUpozila(Upozila $upozila){

        $totalMouza = $this->em->getRepository('PorchaProcessingBundle:Upozila')->getTotalMouzaForUpozila($upozila);

        $upozila->setTotalMouza($totalMouza);
        $this->update($upozila);
    }

    public function updateMouzaCountForDistrict(District $district)
    {
        $totalMouza = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalMouzaForDistrict($district);

        $district->setTotalMouza($totalMouza);
        $this->update($district);
    }

    public function updateKhatianCountForMouza(Mouza $mouza,$surveyType){

        $totalKhatian = $this->em->getRepository('PorchaProcessingBundle:Mouza')->getTotalKhatianForMouza($mouza,$surveyType);

        if(strtoupper($surveyType) == 'RS'){
            $mouza->setTotalKhatianRS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'CS'){
            $mouza->setTotalKhatianCS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'SA'){
            $mouza->setTotalKhatianSA($totalKhatian);
        }
        $this->update($mouza);
    }

    public function updateKhatianCountForUpozila(Upozila $upozila,$surveyType){

        $totalKhatian = $this->em->getRepository('PorchaProcessingBundle:Upozila')->getTotalKhatianForUpozila($upozila,$surveyType);

        if(strtoupper($surveyType) == 'RS'){
            $upozila->setTotalKhatianRS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'CS'){
            $upozila->setTotalKhatianCS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'SA'){
            $upozila->setTotalKhatianSA($totalKhatian);
        }
        $this->update($upozila);

    }

    public function updateKhatianCountForDistrict(District $district, $surveyType){

        $totalKhatian = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalKhatianForDistrict($district,$surveyType);
        if(strtoupper($surveyType) == 'RS'){
            $district->setTotalKhatianRS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'CS'){
            $district->setTotalKhatianCS($totalKhatian);
        }
        if(strtoupper($surveyType) == 'SA'){
            $district->setTotalKhatianSA($totalKhatian);
        }
        $this->update($district);
    }

    public function updateTotalApplicationReceived(){

        $totalApplicationReceived = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalApplicationReceived();

        return count($totalApplicationReceived)>0?$totalApplicationReceived[0]['total']:0;
    }

    public function updateTotalApplicationDelivered(){

        $totalDeliveredApplication = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalApplicationDelivered();

        return count($totalDeliveredApplication)>0?$totalDeliveredApplication[0]['max_total']:'';
    }

    public function updateTotalRecordDigitalized(){

        $totalRecordDigitalized = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalRecordDigitalized();

        return count($totalRecordDigitalized)>0?$totalRecordDigitalized[0]['total']:'';
    }
    public function updateTotalRecordRoom(){

        $totalRecordRoom = $this->em->getRepository('PorchaProcessingBundle:District')->getTotalRecordRoom();

        return count($totalRecordRoom)>0?$totalRecordRoom[0]['total']:'';
    }

    public function updateAllDistrict()
    {
        $districts = $this->em->getRepository('AppBundle:District')->findAll();

        foreach ($districts as $district) {
            $this->updateDistrict($district);
        }

    }

    public function handleTotalUDCServiceDelivered($district){

        $totalUDCAppDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalUDCServiceDelivered($district);
        $khatianDistrict = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('geocode' => $district->getGeocode()));

        $khatianDistrict->setUdcServiceDelivered($totalUDCAppDelivered[0]['total']);
        $this->update($khatianDistrict);

    }
    public function handleTotalServiceDelivered($district){

        $totalAppDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalServiceDelivered($district);
        $khatianDistrict = $this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array('geocode' => $district->getGeocode()));

        $khatianDistrict->setTotalServiceDelivered($totalAppDelivered[0]['total']);
        $this->update($khatianDistrict);

    }

    public function update($data){

        $this->em->persist($data);
        $this->em->flush();
    }

    public function getLastMonthStatistics() {

        $stats = $this->em->getRepository('PorchaProcessingBundle:VrrStatistics')->getStatistics();

        $ret = array();

        if(count($stats)>0){
            $ret[] = array(
                'totalAppReceived' => $stats[0]['totalAppReceived'],
                'totalAppDelivered' => $stats[0]['totalAppDelivered'],
                'totalDigitizedKhatian' => $stats[0]['totalDigitizedKhatian'],
                'totalRecordRoom' => $stats[0]['totalRecordRoom']
            );
        }

        return $ret;
    }
    public function updateDistrictForApplication(ServiceRequest $serviceRequest,$district){

        if($serviceRequest->getStatus() == 'DELIVERED'){

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalKhatianDelivered($district);

                $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
                    'id'=> $district,
                ));
                $district->setTotalKhatianAppDelivered($totalAppKhatianDelivered[0]['total']);

            }else{

                $totalAppOthersDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalOthersAppDelivered($district);
                $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
                    'id'=> $district,
                ));
                $district->setTotalOthersAppDelivered($totalAppOthersDelivered[0]['total']);

            }

        }else{

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalKhatianReceived($district);
                $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
                    'id'=> $district,
                ));
                $district->setTotalKhatianAppReceived($totalAppKhatianReceived[0]['total']);

            }else{

                $totalAppOthersReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequest')->getTotalOthersAppReceived($district);
                $district=$this->em->getRepository('PorchaProcessingBundle:District')->findOneBy(array(
                    'id'=> $district,
                ));
                $district->setTotalOthersAppReceived($totalAppOthersReceived[0]['total']);

            }

        }

        $this->update($district);

    }
    public function updateUpozilaForApplication(ServiceRequest $serviceRequest,$upozila){

        if($serviceRequest->getStatus() == 'DELIVERED'){

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalKhatianDeliveredForUpozila($upozila);

                $upozila=$this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array(
                    'id'=> $upozila
                ));
                $upozila->setTotalKhatianAppDelivered($totalAppKhatianDelivered[0]['total']);

            }else{

                $totalAppOthersDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalOthersAppDeliveredForUpozila($upozila);
                $upozila = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array(
                    'id'=> $upozila
                ));

                $upozila->setTotalOthersAppDelivered($totalAppOthersDelivered[0]['total']);

            }

        }else{

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalKhatianReceivedForUpozila($upozila);

                $upozila = $this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array(
                    'id'=> $upozila
                ));
                $upozila->setTotalKhatianAppReceived($totalAppKhatianReceived[0]['total']);

            }else{

                $totalAppOthersReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalOthersAppReceivedForUpozila($upozila);

                $upozila=$this->em->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array(
                    'id'=> $upozila
                ));
                $upozila->setTotalOthersAppReceived($totalAppOthersReceived[0]['total']);

            }

        }

        $this->update($upozila);

    }
    public function updateMouzaForApplication(ServiceRequest $serviceRequest,$mouza){

        if($serviceRequest->getStatus() == 'DELIVERED'){

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalKhatianDeliveredForMouza($mouza);

                $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findOneBy(array(
                    'id'=> $mouza
                ));
                $mouza->setTotalKhatianAppDelivered($totalAppKhatianDelivered[0]['total']);

            }else{

                $totalAppOthersDelivered = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalOthersAppDeliveredForMouza($mouza);
                $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findOneBy(array(
                    'id'=> $mouza
                ));

                $mouza->setTotalOthersAppDelivered($totalAppOthersDelivered[0]['total']);

            }

        }else{

            if($serviceRequest->getType() == 'PORCHA_REQUEST'){

                $totalAppKhatianReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalKhatianReceivedForMouza($mouza);

                $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findOneBy(array(
                    'id'=> $mouza
                ));
                $mouza->setTotalKhatianAppReceived($totalAppKhatianReceived[0]['total']);

            }else{

                $totalAppOthersReceived = $this->em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getTotalOthersAppReceivedForMouza($mouza);

                $mouza = $this->em->getRepository('PorchaProcessingBundle:Mouza')->findOneBy(array(
                    'id'=> $mouza
                ));

                $mouza->setTotalOthersAppReceived($totalAppOthersReceived[0]['total']);

            }

        }

        $this->update($mouza);

    }
}