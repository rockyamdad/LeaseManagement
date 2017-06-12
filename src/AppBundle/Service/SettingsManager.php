<?php
namespace AppBundle\Service;

use AppBundle\Entity\AdditionalFee;
use AppBundle\Entity\CourtFee;
use AppBundle\Entity\DeliveryDaySettings;
use AppBundle\Entity\District;
use AppBundle\Entity\Office;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Entity\SmsSetting;
use AppBundle\Entity\Upozila;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\SiteMeta;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

class SettingsManager
{
    use QueryAssistant;
    protected $em;
    protected $user;
    protected $office;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->office = $this->user->getOffice();
    }

    public function update($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function getDistrictList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:District')->createQueryBuilder('d');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }

        return $qb->getQuery()->getResult();
    }
    public function getDivisionList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Division')->createQueryBuilder('d');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getUpozilaList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Upozila')->createQueryBuilder('u');
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
    public function getUnionList($data)
    {
        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Union')->createQueryBuilder('u');
        $qb->join('u.upozila', 'up');
        $qb->where('u.deleted = 0');

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        }
        $qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
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
    public function saveDcOfficeSetting(OfficeSettings $officeSettings) {
        $this->em->persist($officeSettings);
        $this->em->flush();
    }

    public function saveDeliveryday(DeliveryDaySettings $deliveryDaySettings) {
        $status= array(
            'success'=> '',
            'error'=> ''
        );
        if($this->office->getType() == 'MINISTRY'){
            $officesettings = $this->em->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('applicationType'=>$deliveryDaySettings->getApplicationType(), 'survey' => $deliveryDaySettings->getSurvey(),'active'=>true));

            if($officesettings){

                foreach($officesettings as $officesetting){
                    $officesetting->setActive(false);
                    $this->em->persist($officesetting);
                }
            }
            $deliveryDaySettings->setOffice($this->office);
            $deliveryDaySettings->setCreatedAt( new \DateTime());
            $this->em->persist($deliveryDaySettings);

            $dcOffices = $this->getOfficesByType('DC');
            foreach ($dcOffices as $dcOffice){
                $deliveryDaySettingsNew = new DeliveryDaySettings();
                $deliveryDaySettingsNew->setSurvey($deliveryDaySettings->getSurvey());
                $deliveryDaySettingsNew->setApplicationType($deliveryDaySettings->getApplicationType());
                $deliveryDaySettingsNew->setNormalDeliveryHasEntry($deliveryDaySettings->getNormalDeliveryHasEntry());
                $deliveryDaySettingsNew->setNormalDeliveryNotEntry($deliveryDaySettings->getEmergencyDeliveryNotEntry());
                $deliveryDaySettingsNew->setNormalDeliveryNonDeliverable($deliveryDaySettings->getNormalDeliveryNonDeliverable());
                $deliveryDaySettingsNew->setEmergencyDeliveryHasEntry($deliveryDaySettings->getEmergencyDeliveryHasEntry());
                $deliveryDaySettingsNew->setEmergencyDeliveryNotEntry($deliveryDaySettings->getEmergencyDeliveryNotEntry());
                $deliveryDaySettingsNew->setEmergencyDeliveryNonDeliverable($deliveryDaySettings->getEmergencyDeliveryNonDeliverable());
                $deliveryDaySettingsNew->setLocked(($deliveryDaySettings->isLocked())? true: false);
                $deliveryDaySettingsNew->setOffice($this->em->getRepository('AppBundle:Office')->findOneBy(array('id'=>$dcOffice->getId())));
                $deliveryDaySettingsNew->setCreatedAt(new \DateTime());
                $this->em->persist($deliveryDaySettingsNew);
            }
            $status['success']= 'This delivery day has been created';
        }else{
            $lockedDeliveryDays = $this->em->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('applicationType'=>$deliveryDaySettings->getApplicationType(), 'survey' => $deliveryDaySettings->getSurvey(),'active'=>true, 'office'=>$this->office,'locked'=>true));
            if(!$lockedDeliveryDays){

                $officesettings = $this->em->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('applicationType'=>$deliveryDaySettings->getApplicationType(),'survey' => $deliveryDaySettings->getSurvey(),'active'=>true, 'office'=>$this->office));

                if($officesettings){

                    foreach($officesettings as $officesetting){
                        $officesetting->setActive(false);
                        $this->em->persist($officesetting);
                    }
                }
                $deliveryDaySettings->setOffice($this->office);
                $deliveryDaySettings->setCreatedAt( new \DateTime());
                $this->em->persist($deliveryDaySettings);
                $status['success']= 'This delivery day has been created';
            }else{
                $status['error']= 'You are not allowed to created this delivery day';
            }

        }

        $this->em->flush();
        return $status;
    }

    public function getDeliveryDayByOffice($data, $appType) {
        $params = $this->queryParameters($data);
        $qb = $this->em->getRepository('AppBundle:DeliveryDaySettings')->createQueryBuilder('d');
        $qb->where('d.office = :office');
        $qb->setParameter('office', $this->office);
        if (empty($data)) {
            $qb->andWhere('d.applicationType = :appType');
            $qb->setParameter('appType', $appType);
        }

        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        return $qb->getQuery()->getResult();
    }

    public function saveCourtFee(CourtFee $courtFee) {
        //var_dump($courtFee);die;
        $status= array(
            'success'=> '',
            'error'=> ''
        );
        if($this->office->getType() == 'MINISTRY'){
            $courtFees = $this->em->getRepository('AppBundle:CourtFee')->findBy(array('applicationType'=>$courtFee->getApplicationType(), 'survey' => $courtFee->getSurvey(),'active'=>true));

            if ($courtFees) {

                foreach ($courtFees as $courtfee) {
                    $courtfee->setActive(false);
                    $this->em->persist($courtfee);
                }
            }
            $courtFee->setOffice($this->office);
            $courtFee->setCreatedAt(new \DateTime());
            $this->em->persist($courtFee);

           $dcOffices = $this->getOfficesByType('DC');
            foreach ($dcOffices as $dcOffice){
                $courtFeeNew = new CourtFee();
                $courtFeeNew->setSurvey($courtFee->getSurvey());
                $courtFeeNew->setApplicationType($courtFee->getApplicationType());
                $courtFeeNew->setNormalCourtFee($courtFee->getNormalCourtFee());
                $courtFeeNew->setEmergencyCourtFee($courtFee->getEmergencyCourtFee());
                $courtFeeNew->setLocked(($courtFee->isLocked())? true: false);
                $courtFeeNew->setOffice($this->em->getRepository('AppBundle:Office')->findOneBy(array('id'=>$dcOffice->getId())));
                $courtFeeNew->setCreatedAt(new \DateTime());
                $this->em->persist($courtFeeNew);
            }
            $status['success']= 'court fee has been created';
        }else{
            $lockedCourtFees = $this->em->getRepository('AppBundle:CourtFee')->findBy(array('survey' => $courtFee->getSurvey(),'applicationType'=>$courtFee->getApplicationType(), 'office'=>$this->office, 'active'=>true, 'locked'=>true));
            if(!$lockedCourtFees){
                $courtFees = $this->em->getRepository('AppBundle:CourtFee')->findBy(array('survey' => $courtFee->getSurvey(),'applicationType'=>$courtFee->getApplicationType(), 'office'=>$this->office, 'active'=>true));
                if ($courtFees) {

                    foreach ($courtFees as $courtfee) {
                        $courtfee->setActive(false);
                        $this->em->persist($courtfee);
                    }
                }
                $courtFee->setOffice($this->office);
                $courtFee->setCreatedAt(new \DateTime());
                $this->em->persist($courtFee);
                $status['success']= 'court fee has been created';
            }else{
                $status['error']= 'You are not allowed to created this court fee';
            }
        }
        $this->em->flush();
        return $status;
    }

    public function getCourtFeeByOffice($data, $appType) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:CourtFee')->createQueryBuilder('c');
        $qb->where('c.office = :office');
        $qb->setParameter('office', $this->office);
        if (empty($data)) {
            $qb->andWhere('c.applicationType = :appType');
            $qb->setParameter('appType', $appType);
        }

        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        return $qb->getQuery()->getResult();
    }

    public function saveAdditionalFee(AdditionalFee $additionalFee) {
        //var_dump($courtFee);die;
        $status= array(
            'success'=> '',
            'error'=> ''
        );
        if($this->office->getType() == 'MINISTRY'){
            $additionalFees = $this->em->getRepository('AppBundle:AdditionalFee')->findBy(array('applicationType'=>$additionalFee->getApplicationType(), 'survey' => $additionalFee->getSurvey(),'active'=>true));

            if ($additionalFees) {

                foreach ($additionalFees as $additional_fee) {
                    $additional_fee->setActive(false);
                    $this->em->persist($additional_fee);
                }
            }
            $additionalFee->setOffice($this->office);
            $additionalFee->setCreatedAt(new \DateTime());
            $this->em->persist($additionalFee);

           $dcOffices = $this->getOfficesByType('DC');
            foreach ($dcOffices as $dcOffice){
                $courtFeeNew = new AdditionalFee();
                $courtFeeNew->setSurvey($additionalFee->getSurvey());
                $courtFeeNew->setApplicationType($additionalFee->getApplicationType());
                $courtFeeNew->setFeeTypeKey1($additionalFee->getFeeTypeKey1());
                $courtFeeNew->setFeeTypeValue1($additionalFee->getFeeTypeValue1());
                $courtFeeNew->setFeeTypeKey2($additionalFee->getFeeTypeKey2());
                $courtFeeNew->setFeeTypeValue2($additionalFee->getFeeTypeValue2());
                $courtFeeNew->setFeeTypeKey3($additionalFee->getFeeTypeKey3());
                $courtFeeNew->setFeeTypeValue3($additionalFee->getFeeTypeValue3());
                $courtFeeNew->setFeeApplicable1($additionalFee->getFeeApplicable1());
                $courtFeeNew->setFeeApplicable2($additionalFee->getFeeApplicable2());
                $courtFeeNew->setFeeApplicable3($additionalFee->getFeeApplicable3());
                $courtFeeNew->setLocked(($additionalFee->isLocked())? true: false);
                $courtFeeNew->setOffice($this->em->getRepository('AppBundle:Office')->findOneBy(array('id'=>$dcOffice->getId())));
                $courtFeeNew->setCreatedAt(new \DateTime());
                $this->em->persist($courtFeeNew);
            }
            $status['success']= 'Additional Fee has been created';
        }else{
            $lockedAdditionalFees = $this->em->getRepository('AppBundle:AdditionalFee')->findBy(array('survey' => $additionalFee->getSurvey(),'applicationType'=>$additionalFee->getApplicationType(), 'office'=>$this->office, 'active'=>true, 'locked'=>true));
            if(!$lockedAdditionalFees){
                $additionalFees = $this->em->getRepository('AppBundle:AdditionalFee')->findBy(array('survey' => $additionalFee->getSurvey(),'applicationType'=>$additionalFee->getApplicationType(), 'office'=>$this->office, 'active'=>true));
                if ($additionalFees) {

                    foreach ($additionalFees as $additional_fee) {
                        $additional_fee->setActive(false);
                        $this->em->persist($additional_fee);
                    }
                }
                $additionalFee->setOffice($this->office);
                $additionalFee->setCreatedAt(new \DateTime());
                $this->em->persist($additionalFee);
                $status['success']= 'Additional Fee has been created';
            }else{
                $status['error']= 'You are not allowed to created this additional fee';
            }
        }
        $this->em->flush();
        return $status;
    }

    public function getAdditionalFeeByOffice($data, $appType) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:AdditionalFee')->createQueryBuilder('a');
        $qb->where('a.office = :office');
        $qb->setParameter('office', $this->office);
        if (empty($data)) {
            $qb->andWhere('a.applicationType = :appType');
            $qb->setParameter('appType', $appType);
        }

        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        return $qb->getQuery()->getResult();
    }

    public function getDistrictUpozilas(District $district) {

        return $this->em->getRepository('AppBundle:Upozila')->findBy(array(
            'district' => $district
        ));

    }
    public function getUpozilasUnions(Upozila $upozila) {

        return $this->em->getRepository('AppBundle:Union')->findBy(array(
            'upozila' => $upozila
        ));

    }

    private function getOfficesByType($type) {

        return $this->em->getRepository('AppBundle:Office')->findBy(array('type' => $type, 'active'=>true));
    }


    public function saveSmsSetting(SmsSetting $smsSetting) {
        $status= array(
            'success'=> '',
            'error'=> ''
        );
        if($this->office->getType() == 'MINISTRY'){
            $smsSettings = $this->em->getRepository('AppBundle:SmsSetting')->findBy(array('applicationType'=>$smsSetting->getApplicationType(),'active'=>true));

            if ($smsSettings) {

                foreach ($smsSettings as $smssetting) {
                    $smssetting->setActive(false);
                    $this->em->persist($smssetting);
                }
            }
            $smsSetting->setOffice($this->office);
            $smsSetting->setCreatedAt(new \DateTime());
            $this->em->persist($smsSetting);

            $dcOffices = $this->getOfficesByType('DC');
            foreach ($dcOffices as $dcOffice){
                $smsSettingNew = new SmsSetting();
                $smsSettingNew->setApplicationType($smsSetting->getApplicationType());
                $smsSettingNew->setNewApplicationMessage($smsSetting->getNewApplicationMessage());
                $smsSettingNew->setDeliveryChangeMessage($smsSetting->getDeliveryChangeMessage());
                $smsSettingNew->setLocked(($smsSetting->isLocked())? true: false);
                $smsSettingNew->setOffice($this->em->getRepository('AppBundle:Office')->findOneBy(array('id'=>$dcOffice->getId())));
                $smsSettingNew->setCreatedAt(new \DateTime());
                $this->em->persist($smsSettingNew);
            }
            $status['success']= 'SMS setting has been created';
        }else{
            $lockedSmsSettings = $this->em->getRepository('AppBundle:SmsSetting')->findBy(array('applicationType'=>$smsSetting->getApplicationType(), 'office'=>$this->office, 'active'=>true, 'locked'=>true));
            if(!$lockedSmsSettings){
                $smsSettings = $this->em->getRepository('AppBundle:SmsSetting')->findBy(array('applicationType'=>$smsSetting->getApplicationType(), 'office'=>$this->office, 'active'=>true));
                if ($smsSettings) {

                    foreach ($smsSettings as $smssetting) {
                        $smssetting->setActive(false);
                        $this->em->persist($smssetting);
                    }
                }
                $smsSetting->setOffice($this->office);
                $smsSetting->setCreatedAt(new \DateTime());
                $this->em->persist($smsSetting);
                $status['success']= 'SMS setting has been created';
            }else{
                $status['error']= 'You are not allowed to created this SMS setting';
            }
        }
        $this->em->flush();
        return $status;
    }


    public function getSmsSettingByOffice($data, $appType) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:SmsSetting')->createQueryBuilder('s');
        $qb->where('s.office = :office');
        $qb->setParameter('office', $this->office);
        if (empty($data)) {
            $qb->andWhere('s.applicationType = :appType');
            $qb->setParameter('appType', $appType);
        }

        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        return $qb->getQuery()->getResult();
    }
}