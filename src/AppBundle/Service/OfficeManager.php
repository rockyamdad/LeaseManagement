<?php
namespace AppBundle\Service;

use AppBundle\Entity\AdditionalFee;
use AppBundle\Entity\CourtFee;
use AppBundle\Entity\DeliveryDaySettings;
use AppBundle\Entity\Office;
use AppBundle\Entity\Holiday;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Entity\SmsSetting;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\SiteMeta;
use AppBundle\Traits\QueryAssistant;
use Doctrine\ORM\EntityManager;

class OfficeManager
{
    use QueryAssistant;
    protected $em;
    protected $user;

    /** @var Office */
    protected $office;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage) {

        $this->em = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        if($this->user != 'anon.') {
            $this->office = $this->user->getOffice();
        }
    }

    public function updateDCOffice(Office $office) {

        $office->setType('DC');
        $office->setRelatedDistricts($office->getDistrict()->getGeocode());
        $this->em->persist($office);
        $settings = $this->em->getRepository('AppBundle:OfficeSettings')->findOneBy(array('office' => $office));
        if (!$settings) {
            $ministry_settings = $this->em->getRepository('AppBundle:OfficeSettings')->findOneBy(array('office' => $this->office));
            $settings = new OfficeSettings();
            $settings->setOffice($office);
            $settings->setApplicationLimitADay($ministry_settings->getApplicationLimitADay());
            $settings->setPostFeeInDistrict($ministry_settings->getPostFeeInDistrict());
            $settings->setPostFeeOutDistrict($ministry_settings->getPostFeeOutDistrict());
            $settings->setUiscApplicationReceiveFee($ministry_settings->getUiscApplicationReceiveFee());
            $settings->setUiscDeliveryDay($ministry_settings->getUiscDeliveryDay());
            $settings->setUiscDeliveryFee($ministry_settings->getUiscDeliveryFee());
            $this->em->persist($settings);
        }
        $DeliveryDaySettings = $this->em->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('office' => $office));
        if(!$DeliveryDaySettings){
            $ministry_deliveryDays = $this->em->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('office' => $this->office, 'active'=>true));

            foreach($ministry_deliveryDays as $ministry_deliveryDay){
                $DeliveryDaySettings = new DeliveryDaySettings();
                $DeliveryDaySettings->setOffice($office);
                $DeliveryDaySettings->setActive(true);
                $DeliveryDaySettings->setSurvey($ministry_deliveryDay->getSurvey());
                $DeliveryDaySettings->setApplicationType($ministry_deliveryDay->getApplicationType());
                $DeliveryDaySettings->setNormalDeliveryHasEntry($ministry_deliveryDay->getNormalDeliveryHasEntry());
                $DeliveryDaySettings->setNormalDeliveryNotEntry($ministry_deliveryDay->getNormalDeliveryNotEntry());
                $DeliveryDaySettings->setNormalDeliveryNonDeliverable($ministry_deliveryDay->getNormalDeliveryNonDeliverable());
                $DeliveryDaySettings->setEmergencyDeliveryHasEntry($ministry_deliveryDay->getEmergencyDeliveryHasEntry());
                $DeliveryDaySettings->setEmergencyDeliveryNotEntry($ministry_deliveryDay->getEmergencyDeliveryNotEntry());
                $DeliveryDaySettings->setEmergencyDeliveryNonDeliverable($ministry_deliveryDay->getEmergencyDeliveryNonDeliverable());
                $DeliveryDaySettings->setCreatedAt( new \DateTime());
                $DeliveryDaySettings->setLocked(($ministry_deliveryDay->isLocked())? true:false);
                $this->em->persist($DeliveryDaySettings);
            }
        }
        $courtFees = $this->em->getRepository('AppBundle:CourtFee')->findBy(array('office' => $office));
        if(!$courtFees){
            $ministry_courtFees = $this->em->getRepository('AppBundle:CourtFee')->findBy(array('office' => $this->office, 'active'=>true));

            foreach($ministry_courtFees as $ministry_courtFee){
                $courtFees = new CourtFee();
                $courtFees->setOffice($office);
                $courtFees->setActive(true);
                $courtFees->setApplicationType($ministry_courtFee->getApplicationType());
                $courtFees->setSurvey($ministry_courtFee->getSurvey());
                $courtFees->setNormalCourtFee($ministry_courtFee->getNormalCourtFee());
                $courtFees->setEmergencyCourtFee($ministry_courtFee->getEmergencyCourtFee());
                $courtFees->setCreatedAt( new \DateTime());
                $courtFees->setLocked(($ministry_courtFee->isLocked())? true:false);
                $this->em->persist($courtFees);
            }
        }
//        additional fee

        $additionalFees = $this->em->getRepository('AppBundle:AdditionalFee')->findBy(array('office' => $office));
        if(!$additionalFees){
            $ministry_additionalFees = $this->em->getRepository('AppBundle:AdditionalFee')->findBy(array('office' => $this->office, 'active'=>true));

            foreach($ministry_additionalFees as $ministry_additionalFee){
                $additionalFees = new AdditionalFee();
                $additionalFees->setOffice($office);
                $additionalFees->setActive(true);
                $additionalFees->setApplicationType($ministry_additionalFee->getApplicationType());
                $additionalFees->setSurvey($ministry_additionalFee->getSurvey());
                $additionalFees->setFeeTypeKey1($ministry_additionalFee->getFeeTypeKey1());
                $additionalFees->setFeeTypeKey2($ministry_additionalFee->getFeeTypeKey2());
                $additionalFees->setFeeTypeKey3($ministry_additionalFee->getFeeTypeKey3());
                $additionalFees->setFeeTypeValue1($ministry_additionalFee->getFeeTypeValue1());
                $additionalFees->setFeeTypeValue2($ministry_additionalFee->getFeeTypeValue2());
                $additionalFees->setFeeTypeValue3($ministry_additionalFee->getFeeTypeValue3());
                $additionalFees->setFeeApplicable1($ministry_additionalFee->getFeeApplicable1());
                $additionalFees->setFeeApplicable2($ministry_additionalFee->getFeeApplicable2());
                $additionalFees->setFeeApplicable3($ministry_additionalFee->getFeeApplicable3());
                $additionalFees->setCreatedAt( new \DateTime());
                $additionalFees->setLocked(($ministry_additionalFee->isLocked())? true:false);
                $this->em->persist($additionalFees);
            }
        }
//        sms_setting

        $smsSettings = $this->em->getRepository('AppBundle:SmsSetting')->findBy(array('office' => $office));
        if(!$smsSettings){
            $ministry_smsSettings = $this->em->getRepository('AppBundle:SmsSetting')->findBy(array('office' => $this->office, 'active'=>true));

            foreach($ministry_smsSettings as $ministry_smsSetting){
                $smsSetting = new SmsSetting();
                $smsSetting->setOffice($office);
                $smsSetting->setActive(true);
                $smsSetting->setApplicationType($ministry_smsSetting->getApplicationType());
                $smsSetting->setNewApplicationMessage($ministry_smsSetting->getNewApplicationMessage());
                $smsSetting->setDeliveryChangeMessage($ministry_smsSetting->getDeliveryChangeMessage());
                $smsSetting->setCreatedAt( new \DateTime());
                $smsSetting->setLocked(($ministry_smsSetting->isLocked())? true:false);
                $this->em->persist($smsSetting);
            }
        }

        $this->em->flush();
    }

    public function updateACLandOffice(Office $office) {

        $office->setType('AC_LAND');
        $office->setDistrict($office->getParent()->getDistrict());
        $office->setRelatedDistricts($office->getParent()->getDistrict()->getGeocode());
        $this->em->persist($office);
        $this->em->flush();
    }

    public function getOfficeList($type, $data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Office')->createQueryBuilder('o');
        $qb->join('o.district', 'd');
        $qb->leftJoin('d.division', 'dv');
        $qb->leftJoin('o.upozila', 'u');
        $qb->where('o.type = :type');
        $qb->setParameter('type', strtoupper($type));

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('dv.name', 'asc');
            $qb->orderBy('d.name', 'asc');
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        $this->singleSearchQuery($qb, $params['arrSingleSearchField']);

        return $qb->getQuery()->getResult();
    }

    public function getUdcOfficeList($data) {

        $params = $this->queryParameters($data);

        $qb = $this->em->getRepository('AppBundle:Office')->createQueryBuilder('o');
        $qb->join('o.district', 'd');
        $qb->leftJoin('d.division', 'dv');
        $qb->leftJoin('o.upozila', 'u');
        if(strtoupper($this->office->getType()) == 'UDC'){

        $qb->where("o.type = 'UDC'");
        $qb->andWhere('o.parent =:parent');
        $qb->setParameter('parent',$this->office);
        }elseif(strtoupper($this->office->getType()) == 'DC'){
            $qb->andWhere('o.parent =:parent');
            $qb->setParameter('parent',$this->office);
        }
        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('dv.name', 'asc');
            $qb->orderBy('d.name', 'asc');
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);

        return $qb->getQuery()->getResult();
    }

    public function getOfficeUpozilas(Office $office) {

        return $this->em->getRepository('AppBundle:Upozila')->findBy(array(
            'district' => $office->getDistrict()
        ));
    }

    public function getOfficeListByType($type) {

        $qb = $this->em->getRepository('AppBundle:Office')->createQueryBuilder('o');
        $qb->join('o.district', 'd');
        $qb->leftJoin('o.upozila', 'u');
        $qb->where('o.type = :type');
        $qb->setParameter('type', $type);
        return $qb->getQuery()->getResult();
    }

    public function getHolidayList($data) {

        $data['year'] = (!isset($data['year'])) ? date('Y') : $data['year'];
        $data['month'] = (!isset($data['month'])) ? date('m') : $data['month'];

        $params = $this->queryParameters($data);
        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');

        if($this->office->getType() != 'MINISTRY'){
            $qb->where('h.office = :office');
            $qb->setParameter('office', $this->office);
            $qb->orWhere($qb->expr()->isNull('h.office'));
        }

        if (!empty($params['orderField'])) {
            $qb->orderBy($params['orderField'], $params['order']);
        } else {
            $qb->orderBy('h.date', 'asc');
        }
        if($data['month']==12){
            $month_day= $data['month'].'-31';
        }else{
            $month_day = ($data['month'] + 1).'-01 -1 day';
        }
        $qb = $this->filterQuery($qb, $params['arrFilterField']);
        //$qb = $this->singleSearchQuery($qb, $params['arrSingleSearchField']);
        $qb = $this->rangeFilterQuery($qb, array('h.date' => date("Y-m-d", strtotime($data['year'].'-'.$data['month'].'-01'))), array('h.date' =>date("Y-m-d", strtotime($data['year'].'-'.$month_day))));

        return $qb->getQuery()->getResult();
    }

    public function getHolidayListByType($year, $type) {

//        $params = $this->queryParameters($data);
        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');

        if($this->office->getType() != 'MINISTRY'){
            $qb->where('h.office = :office');
            $qb->setParameter('office', $this->office);
            $qb->orWhere($qb->expr()->isNull('h.office'));
        }
            $qb->andWhere('h.type = :type');
            $qb->setParameter('type', $type);
            $qb->orderBy('h.date', 'asc');

        $qb = $this->rangeFilterQuery($qb, array('h.date' => date("Y-m-d", strtotime($year.'-1-01'))), array('h.date' =>date("Y-m-d", strtotime($year.'-12-31'))));

        return $qb->getQuery()->getResult();
    }

    public function saveHoliday($data) {
        $status= array(
            'success'=> '',
            'error'=> ''
        );
        $type = $data['appbundle_holiday']['type'];
        $title = ($data['appbundle_holiday']['title'])? $data['appbundle_holiday']['title']:$data['appbundle_holiday']['type'] ;

        if($this->office->getType() == 'MINISTRY'){
            $office_id = null;

            if($type=='WEEK_LEAVE'){
                if(isset($data['appbundle_holiday']['day'])){
                    $year = $data['appbundle_holiday']['year'];
                    $days = $data['appbundle_holiday']['day'];

                    foreach ($days as $day){
                        foreach ($this->getDateBydays($year, $day) as $value) {
                            $getExistHolidays = $this->em->getRepository('AppBundle:Holiday')->findBy(array('date' => $value));
                            if(!$getExistHolidays){
                                $holiday = new Holiday();
                                $holiday->setType($type);
                                $holiday->setTitle($title);
                                $holiday->setDate($value);
                                $holiday->setOffice($office_id);
                                $this->em->persist($holiday);
                            }
                        }
                    }
                }else{
                    $status['error']= 'Please select a day';
                }

            }else{

                if($data['appbundle_holiday']['startDate']!=''&& $data['appbundle_holiday']['endDate']!=''){

                    $startDate = $data['appbundle_holiday']['startDate'];
                    $endDate = $data['appbundle_holiday']['endDate'];

                    foreach ($this->getDateByStartEndDate($startDate, $endDate) as $value) {
                        $getExistHolidays = $this->em->getRepository('AppBundle:Holiday')->findBy( array('date' => $value));
                        if(!$getExistHolidays) {
                            $holiday = new Holiday();
                            $holiday->setType($type);
                            $holiday->setTitle($title);
                            $holiday->setDate($value);
                            $holiday->setOffice($office_id);
                            $this->em->persist($holiday);
                        }else {
                            foreach($getExistHolidays as $exitHoliday){
                                $exitHoliday->setType($type);
                                $exitHoliday->setTitle($title);
                                $exitHoliday->setDate($value);
                                $exitHoliday->setOffice($office_id);
                                $this->em->persist($exitHoliday);
                            }
                        }
                    }
                }else{
                    $status['error']= 'Please select start date and end date';
                }
            }

        }else{

            $office_id = $this->office;

            if($type=='WEEK_LEAVE'){

                    $status['error']= 'You are not allowed to create this holiday';
            }else{

                if($data['appbundle_holiday']['startDate']!=''&& $data['appbundle_holiday']['endDate']!=''){

                    $startDate = $data['appbundle_holiday']['startDate'];
                    $endDate = $data['appbundle_holiday']['endDate'];

                    foreach ($this->getDateByStartEndDate($startDate, $endDate) as $value) {
                        $getExistHolidays = $this->em->getRepository('AppBundle:Holiday')->getHolidayOwnOfficeExit( $value, $office_id);
                        if(!$getExistHolidays) {
                            $holiday = new Holiday();
                            $holiday->setType($type);
                            $holiday->setTitle($title);
                            $holiday->setDate($value);
                            $holiday->setOffice($office_id);
                            $this->em->persist($holiday);
                        }
                    }
                }else{
                    $status['error']= 'Please select start date and end date';
                }
            }
        }

        $this->em->flush();
        return $status;
    }

    private  function getDateBydays($y, $d)
    {
        $start    = new \DateTime("first $d of $y-01");
        $end      = new \DateTime("last day of $y-12");
        $end = $end->modify( '+1 day' );
        return new \DatePeriod( $start, \DateInterval::createFromDateString("next $d"), $end
        );
    }

    private  function getDateByStartEndDate($startDate, $endDate)
    {
        $start    = new \DateTime($startDate);
        $end      = new \DateTime($endDate);
        $end = $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        return new \DatePeriod($start, $interval, $end);

    }

    public function applicationDeliveryDate($appDate, $dayCount){

        $i=1;
        while ($i<= $dayCount) {

            $appDate = date('Y-m-d',strtotime($appDate.' +1 day'));

            if (!$this->isDateInHolidays($appDate)) {
                if($i==$dayCount){
                    return $appDate;
                }
                $i++;
            }
        }
    }

    private function isDateInHolidays($date)
    {
        /** TODO: URGENT NEED TO FIX FOR VRR. Temp solution to avoid confilict */
        if (!$this->office) {
            return false;
        }

        $office = $this->office;
        if(strtoupper($this->office->getType()) == 'UDC'){
           $office =  $this->office->getParent();
        }

        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');
        $qb->select('COUNT(h.id)');
        $qb->Where('h.office = :office');
        $qb->setParameter('office', $office);
        $qb->orWhere($qb->expr()->isNull('h.office'));
        $qb->andWhere('h.date = :date');
        $qb->setParameter('date', $date);

        return ($qb->getQuery()->getSingleScalarResult()) ? true : false;
    }

    public function holidayOverview($data){
        $year = (!isset($data)) ? date('Y') : $data;
        $totalDay = $this->numberOfDaysInYear($year);

        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');
        $qb->select('COUNT(h.id) as weekLeave');
        $qb->Where('h.type = :type');
        $qb->setParameter('type', 'WEEK_LEAVE');
        $qb->andWhere(
            $qb->expr()->between(
                'h.date',
                ':dateFrom',
                ':dateTo'
            )
        )
            ->setParameter('dateFrom', $year.'-01-01')
            ->setParameter('dateTo', $year.'-12-31');
        $weekLeave = $qb->getQuery()->getSingleScalarResult();

        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');
        $qb->select('COUNT(h.id) as govLeave');
        if($this->office->getType() != 'MINISTRY'){
            $qb->where('h.office = :office');
            $qb->setParameter('office', $this->office);
            $qb->orWhere($qb->expr()->isNull('h.office'));
        }
        $qb->andWhere('h.type = :type');
        $qb->setParameter('type', 'GOV_LEAVE');
        $qb->andWhere(
            $qb->expr()->between(
                'h.date',
                ':dateFrom',
                ':dateTo'
            )
        )
            ->setParameter('dateFrom', $year.'-01-01')
            ->setParameter('dateTo', $year.'-12-31');
        $govLeave = $qb->getQuery()->getSingleScalarResult();

        $qb = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h');

        $qb->select('COUNT(h.id) as ceoLeave');
        if($this->office->getType() != 'MINISTRY'){
            $qb->where('h.office = :office');
            $qb->setParameter('office', $this->office);
            $qb->orWhere($qb->expr()->isNull('h.office'));
        }
        $qb->andWhere('h.type = :type');
        $qb->setParameter('type', 'CEO_LEAVE');
        /*$qb->andWhere('h.office = :office');
        $qb->setParameter('office', $office_id);*/
        $qb->andWhere(
            $qb->expr()->between(
                'h.date',
                ':dateFrom',
                ':dateTo'
            )
        )
            ->setParameter('dateFrom', $year.'-01-01')
            ->setParameter('dateTo', $year.'-12-31');
        $ceoLeave = $qb->getQuery()->getSingleScalarResult();

        $totalLeave = $weekLeave + $govLeave + $ceoLeave;
        $workingDay = $totalDay - $totalLeave;
        $data = array(
            'searchYear'     => $year,
            'totalDay'=>   $totalDay,
            'totalLeave'=>  $totalLeave,
            'workingDay'=>  $workingDay,
            'weekLeave'=>   $weekLeave,
            'govLeave'=>   $govLeave,
            'ceoLeave'=>   $ceoLeave,
        );

        return $data;
    }

    private function numberOfDaysInYear($year){
       return date("z", mktime(0,0,0,12,31,$year)) + 1;
    }

    private function getOfficesByType($type) {

        return $this->em->getRepository('AppBundle:Office')->findBy(array('type' => $type));
    }
}