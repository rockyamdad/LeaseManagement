<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdditionalFee;
use AppBundle\Entity\CourtFee;
use AppBundle\Entity\DeliveryDaySettings;
use AppBundle\Entity\District;
use AppBundle\Entity\Division;
use AppBundle\Entity\Document;
use AppBundle\Entity\Office;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Entity\SmsSetting;
use AppBundle\Entity\Union;
use AppBundle\Entity\Upozila;
use AppBundle\Form\AdditionalFeeType;
use AppBundle\Form\CourtFeeType;
use AppBundle\Form\DeliveryDayType;
use AppBundle\Form\DistrictType;
use AppBundle\Form\DCOfficeSettingType;
use AppBundle\Form\DivisionType;
use AppBundle\Form\SmsSettingType;
use AppBundle\Form\UnionType;
use AppBundle\Form\UpozilaType;
use AppBundle\Model\CsvFileIterator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use UserBundle\Entity\Profile;
use UserBundle\Entity\User;

class SettingsController extends Controller
{

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_DISTRICTS")
     */
    public function updateDistrictAction(Request $request, District $district_ = null)
    {
        $district = new District();
        if ($district_) {
            $district = $district_;
        }

        $form = $this->createForm(new DistrictType(), $district);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.settings_manager')->update($district);

                $this->addFlash('success',$this->get('translator')->trans('District added'));
                return $this->redirect($this->generateUrl('app_district_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('app_create_district');
        $data['district'] = $district;
        if ($district->getId()) {
            $data['form_action'] = $this->generateUrl('app_update_district', array('id' => $district->getId()));
            $data['title'] = 'Edit District';
        }

        return $this->render('AppBundle:Settings:update_district.html.twig', $data);
    }
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_DISTRICTS")
     */
    public function updateDivisionAction(Request $request, Division $division_ = null)
    {

        $division = new Division();
        if ($division_) {
            $division = $division_;
        }

        $form = $this->createForm(new DivisionType(), $division);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.settings_manager')->update($division);

                $this->addFlash('success',$this->get('translator')->trans('Division added'));

                return $this->redirect($this->generateUrl('app_division_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('app_create_division');
        $data['division'] = $division;
        if ($division->getId()) {
            $data['form_action'] = $this->generateUrl('app_update_division', array('id' => $division->getId()));
            $data['title'] = 'Edit Division';
        }

        return $this->render('AppBundle:Settings:update_division.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_UPOZILAS")
     */
    public function updateUpozilaAction(Request $request, Upozila $upozila_ = null)
    {
        $upozila = new Upozila();
        if ($upozila_) {
            $upozila = $upozila_;
        }

        $form = $this->createForm(new UpozilaType($this->getUser(), $this->get('security.authorization_checker')->isGranted('ROLE_GEOGRAPHICAL_INFO_APPROVED')), $upozila);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.settings_manager')->update($upozila);

                $msg = ($upozila->getId()) ? 'Upozila updated' : 'Upozila added';
                $this->addFlash('success',  $this->get('translator')->trans($msg));

                return $this->redirect($this->generateUrl('app_upozila_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('app_create_upozila');
        $data['upozila'] = $upozila;
        if ($upozila->getId()) {
            $data['form_action'] = $this->generateUrl('app_update_upozila', array('id' => $upozila->getId()));
            $data['title'] = 'Edit Upozila';
        }

        return $this->render('AppBundle:Settings:update_upozila.html.twig', $data);
    }


    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_UPOZILAS")
     */
    public function updateUnionAction(Request $request, Union $union_ = null)
    {


        $union = new Union();
        if ($union_) {
            $union = $union_;
        }

        $form = $this->createForm(new UnionType(),$union);

        if ($union_) {
            $form->get('district')->setData($union->getUpozila()->getDistrict());
        }
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.settings_manager')->update($union);

                $msg = ($union->getId()) ? 'Union updated' : 'Union added';
                $this->addFlash('success',  $this->get('translator')->trans($msg));

                return $this->redirect($this->generateUrl('app_union_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('app_create_union');
        $data['union'] = $union;
        if ($union->getId()) {
            $data['form_action'] = $this->generateUrl('app_update_union', array('id' => $union->getId()));
            $data['title'] = 'Edit Union';
        }
        return $this->render('AppBundle:Settings:update_union.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_DISTRICTS")
     */
    public function districtListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importDistricts($document, $app = true);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('app_district_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $districts = $this->get('app.service.settings_manager')->getDistrictList($request->query->all());
        $data['districts']  = $this->get('knp_paginator')->paginate($districts, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Settings:district_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('app_district_list');
            return $this->render('AppBundle:Settings:district_list.html.twig', $data);
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_DISTRICTS")
     */
    public function divisionListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importDivisions($document, $app = true);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('app_division_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $divisions = $this->get('app.service.settings_manager')->getDivisionList($request->query->all());
        $data['divisions']  = $this->get('knp_paginator')->paginate($divisions, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Settings:division_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('app_division_list');
            return $this->render('AppBundle:Settings:division_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_UPOZILAS")
     */
    public function upozilaListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importUpozilas($document, $app = true);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('app_upozila_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $upozilas = $this->get('app.service.settings_manager')->getUpozilaList($request->query->all());
        $data['upozilas']  = $this->get('knp_paginator')->paginate($upozilas, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Settings:upozila_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('app_upozila_list');
            $data['districts'] = $this->getDoctrine()->getRepository('AppBundle:District')->findBy(array('deleted' => 0));
            return $this->render('AppBundle:Settings:upozila_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_UPOZILAS")
     */
    public function unionListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importUnions($document, $app = true);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('app_union_list'));
            }
        }
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $unions = $this->get('app.service.settings_manager')->getUnionList($request->query->all());
        $data['unions']  = $this->get('knp_paginator')->paginate($unions, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Settings:union_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('app_union_list');
            return $this->render('AppBundle:Settings:union_list.html.twig', $data);
        }
    }

    private function createImportForm($document)
    {
        return $this->createFormBuilder($document)
            ->add('file', 'file', array(
                'required' => true,
                'constraints' => array(
                    new File(array(
                        'mimeTypes' => array(
                            'application/vnd.ms-excel',
                            'text/plain',
                            'text/csv',
                            'text/tsv'
                        ),
                        'mimeTypesMessage' => 'Only CSV files are allowed'
                    ))
                )
            ))
            ->getForm();
    }

    public function bulkImportDataAction(Request $request) {

        $this->get('app.model.data_import_manager')->nonDeliverableMessageImport();
    }

    public function dcOfficeSettingAction(Request $request) {

        $officesetting = $this->getDoctrine()->getRepository('AppBundle:OfficeSettings')->findOneBy(array('office' => $this->getUser()->getOffice()));
        $form = $this->createForm(new DCOfficeSettingType(), $officesetting);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->get('app.service.settings_manager')->saveDcOfficeSetting($officesetting);
                $this->addFlash('success',$this->get('translator')->trans('Setting Updated'));
                return $this->redirect($this->generateUrl('dc_office_setting_update'));
            }
        }

        $data['officesetting'] = $officesetting;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('dc_office_setting_update');
        $data['title'] = 'Edit DC Office';

        return $this->render('AppBundle:Settings:dc_office_setting.html.twig',$data);

    }

    public function deliverydayListAction(Request $request, $appType) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $holidays = $this->get('app.service.settings_manager')->getDeliveryDayByOffice($request->query->all(), $appType);
        $data['deliverydays']  = $this->get('knp_paginator')->paginate($holidays, $page, $perPage);
        $data['applicationTypes'] = array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map');

        if ($request->isXmlHttpRequest()) {
            $ff = $request->query->get('ff');
            $data['appType'] = $ff['d.applicationType'];
            return $this->render('AppBundle:DeliveryDay:deliveryday_list_sub.html.twig', $data);
        } else {
            $data['appType'] = $appType;
            $data['search_url'] = $this->generateUrl('deliveryday_list');
            return $this->render('AppBundle:DeliveryDay:deliveryday_list.html.twig', $data);
        }
    }

    public function createDeliverydayAction(Request $request, DeliveryDaySettings $deliveryDaySettings_=null)
    {
        $deliveryday = new DeliveryDaySettings();
        if ($deliveryDaySettings_) {
            $deliveryday = $deliveryDaySettings_;
        }
        $form = $this->createForm(new DeliveryDayType(), $deliveryday);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $message = $this->get('app.service.settings_manager')->saveDeliveryday($deliveryday);
                if($message['success']){
                    $msg= $message['success'];
                }else{
                    $msg= $message['error'];
                }
                $this->addFlash("success", $this->get('translator')->trans($msg));
                return $this->redirect($this->generateUrl('deliveryday_list', array('appType' => $form->getData()->getApplicationType())));
            }
        }
        $data['current_user_office'] = $this->getUser()->getOffice();
        $data['deliveryday'] = $deliveryday;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_deliveryday');
        if ($deliveryday->getId()) {
            $data['form_action'] = $this->generateUrl('create_deliveryday');
            //$data['form_action'] = $this->generateUrl('update_delivery', array('id' => $deliveryday->getId()));
            $data['title'] = 'Edit Delivery day';
        }
        return $this->render('AppBundle:DeliveryDay:create_delivery_day.html.twig',$data);
    }

    public function deleteDeliverydayAction(DeliveryDaySettings $deliveryDaySettings){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:DeliveryDaySettings')->find($deliveryDaySettings);
        $current_user_office = $this->getUser()->getOffice();
            if($current_user_office == $entity->getOffice() && $entity->getOffice() != null){
                $em->remove($entity);
                $this->addFlash("success", $this->get('translator')->trans("This delivery day has been deleted"));
            }else{
                $this->addFlash("error", $this->get('translator')->trans("You are not allowed to remove this delivery day"));
            }


        $em->flush();

        return $this->redirect($this->generateUrl('deliveryday_list'));
    }

    public function courtFeeListAction(Request $request, $appType) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $courtfee = $this->get('app.service.settings_manager')->getCourtFeeByOffice($request->query->all(), $appType);
        $data['courtfees']  = $this->get('knp_paginator')->paginate($courtfee, $page, $perPage);
        $data['applicationTypes'] = array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map');

        if ($request->isXmlHttpRequest()) {
            $ff = $request->query->get('ff');
            $data['appType'] = $ff['c.applicationType'];
            return $this->render('AppBundle:CourtFee:courtfee_list_sub.html.twig', $data);
        } else {
            $data['appType'] = $appType;
            $data['search_url'] = $this->generateUrl('courtfee_list');
            return $this->render('AppBundle:CourtFee:courtfee_list.html.twig', $data);
        }
    }

    public function createCourtFeeAction(Request $request, CourtFee $courtFee_=null)
    {
        $courtfee = new CourtFee();
        if ($courtFee_) {
            $courtfee = $courtFee_;
        }
        $form = $this->createForm(new CourtFeeType(), $courtfee);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $message = $this->get('app.service.settings_manager')->saveCourtFee($courtfee);
                if($message['success']){
                    $msg= $message['success'];
                }else{
                    $msg= $message['error'];
                }
                $this->addFlash("success", $this->get('translator')->trans($msg));
                return $this->redirect($this->generateUrl('courtfee_list', array('appType' => $form->getData()->getApplicationType())));
            }
        }
        $data['current_user_office'] = $this->getUser()->getOffice();
        $data['courtfee'] = $courtfee;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_courtfee');
        if ($courtfee->getId()) {
            $data['form_action'] = $this->generateUrl('create_courtfee');
            $data['title'] = 'Edit Court Fee';
        }
        return $this->render('AppBundle:CourtFee:create_court_fee.html.twig',$data);
    }

    public function deleteCourtFeeAction(CourtFee $courtFee){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:CourtFee')->find($courtFee);
        $current_user_office = $this->getUser()->getOffice();
            if($current_user_office == $entity->getOffice() && $entity->getOffice() != null){
                $em->remove($entity);
                $this->addFlash("success", $this->get('translator')->trans("This court fee has been deleted"));
            }else{
                $this->addFlash("error", $this->get('translator')->trans("You are not allowed to remove this court fee"));
            }
        $em->flush();

        return $this->redirect($this->generateUrl('courtfee_list'));
    }

//    Additional Fee

    public function additionalFeeListAction(Request $request, $appType) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $additionalfee = $this->get('app.service.settings_manager')->getAdditionalFeeByOffice($request->query->all(), $appType);
        $data['additionalfees']  = $this->get('knp_paginator')->paginate($additionalfee, $page, $perPage);
        $data['applicationTypes'] = array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map');

        if ($request->isXmlHttpRequest()) {
            $ff = $request->query->get('ff');
            $data['appType'] = $ff['a.applicationType'];
            return $this->render('AppBundle:AdditionalFee:additional_fee_list_sub.html.twig', $data);
        } else {
            $data['appType'] = $appType;
            $data['search_url'] = $this->generateUrl('additionalfee_list');
            return $this->render('AppBundle:AdditionalFee:additional_fee_list.html.twig', $data);
        }
    }

    public function createAdditionalFeeAction(Request $request, AdditionalFee $additionalFee_=null)
    {
        $additionalfee = new AdditionalFee();
        if ($additionalFee_) {
            $additionalfee = $additionalFee_;
        }
        $form = $this->createForm(new AdditionalFeeType(), $additionalfee);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $message = $this->get('app.service.settings_manager')->saveAdditionalFee($additionalfee);
                if($message['success']){
                    $msg= $message['success'];
                }else{
                    $msg= $message['error'];
                }
                $this->addFlash("success", $this->get('translator')->trans($msg));
                return $this->redirect($this->generateUrl('additionalfee_list', array('appType' => $form->getData()->getApplicationType())));
            }
        }
        $data['current_user_office'] = $this->getUser()->getOffice();
        $data['additionalfee'] = $additionalfee;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_additionalfee');
        if ($additionalfee->getId()) {
            $data['form_action'] = $this->generateUrl('create_additionalfee');
            $data['title'] = 'Edit Court Fee';
        }
        return $this->render('AppBundle:AdditionalFee:create_additional_fee.html.twig',$data);
    }

    public function districtUpozilasAction(District $district) {

        $rows = $this->get('app.service.settings_manager')->getDistrictUpozilas($district);

        $data = array();
        foreach ($rows as $row) {
            $data [] = '<option value="' . $row->getId() . '">' . $row->getName() . '</option>';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    public function upozilasUnionsAction(Upozila $upozila) {

        $rows = $this->get('app.service.settings_manager')->getUpozilasUnions($upozila);

        $data = array();
        foreach ($rows as $row) {
            $data [] = '<option value="' . $row->getId() . '">' . $row->getName() . '</option>';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function comboUpozilaAction($districtId)
    {
        $district = $this->getDoctrine()->getRepository('AppBundle:District')->findOneBy(array('id' => $districtId, 'approved' => 1, 'deleted' => 0));
        $upozilas = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findBy(array('district' => $district, 'approved' => 1, 'deleted' => 0));
        $ret = [];
        foreach ($upozilas as $upozila) {
            $ret[] = ['id' => $upozila->getId(), 'text' => $upozila->getName() ];
        }
        return new JsonResponse($ret);
    }
    public function comboUnionAction($upozilaId)
    {
        $upozila = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findOneBy(array('id' => $upozilaId, 'approved' => 1, 'deleted' => 0));
        $unions = $this->getDoctrine()->getRepository('AppBundle:Union')->findBy(array('upozila' => $upozila, 'approved' => 1, 'deleted' => 0));
        $ret = [];
        foreach ($unions as $union) {
            $ret[] = ['id' => $union->getId(), 'text' => $union->getName() ];
        }
        return new JsonResponse($ret);
    }



    public function smsSettingListAction(Request $request, $appType) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);

        $courtfee = $this->get('app.service.settings_manager')->getSmsSettingByOffice($request->query->all(), $appType);
        $data['smssettings']  = $this->get('knp_paginator')->paginate($courtfee, $page, $perPage);
        $data['applicationTypes'] = array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map');

        if ($request->isXmlHttpRequest()) {
            $ff = $request->query->get('ff');
            $data['appType'] = $ff['s.applicationType'];
            return $this->render('AppBundle:SmsSetting:sms_list_sub.html.twig', $data);
        } else {
            $data['appType'] = $appType;
            $data['search_url'] = $this->generateUrl('sms_setting_list');
            return $this->render('AppBundle:SmsSetting:sms_list.html.twig', $data);
        }
    }

    public function createSmsSettingAction(Request $request, SmsSetting $smsSetting_=null)
    {
        $smsSetting = new SmsSetting();
        if ($smsSetting_) {
            $smsSetting = $smsSetting_;
        }
        $form = $this->createForm(new SmsSettingType(), $smsSetting);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $message = $this->get('app.service.settings_manager')->saveSmsSetting($smsSetting);
                if($message['success']){
                    $msg= $message['success'];
                }else{
                    $msg= $message['error'];
                }
                $this->addFlash("success", $this->get('translator')->trans($msg));
                return $this->redirect($this->generateUrl('sms_setting_list', array('appType' => $form->getData()->getApplicationType())));
            }
        }
        $data['current_user_office'] = $this->getUser()->getOffice();
        $data['smssetting'] = $smsSetting;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_sms_setting');
        if ($smsSetting->getId()) {
            $data['form_action'] = $this->generateUrl('create_sms_setting');
            $data['title'] = 'Edit SMS Setting';
        }
        return $this->render('AppBundle:SmsSetting:create_sms.html.twig',$data);
    }

    /*public function udcDataListAction(Request $request)
    {
                 $document = __DIR__.'/../../../web/csv/dcms_data.csv';

                $this->get('app.model.data_import_manager')->importDataIntoDivisionDistrictUpozillaUnions($document, $app = true);
                return $this->redirect($this->generateUrl('app_district_list'));
    }*/

    public function createAllDCAdminsAction()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $rootDir = $this->container->getParameter('assetic.write_to');
        $data = new CsvFileIterator($rootDir . '/csv/dc_admin_users.csv');

        $i = 0;
        foreach ($data as $row) {
            if ($i == 0 || !isset($row[5])) {$i++; continue;}

            $user = $userManager->findUserByUsername($row[2]);
            if ($user) {
                continue;
            }

            $user = new User();
            $profile = new Profile();

            $profile->setUser($user);
            $profile->setFullNameEn($row[5]);

            $user->setUsername($row[2]);
            $user->setEmail($row[3]);
            $user->setPlainPassword($row[4]);
            $user->setEnabled(1);
            $user->setProfile($profile);

            $group = $this->getDoctrine()->getEntityManager()->getRepository('RbsUserBundle:Group')->findOneBy(array('name' => $row[0]));
            if ($group) {
                $user->setGroups(array($group));
            }
            $userManager->updateUser($user);
        }

        echo 'DC Admins created';
    }

    public function createAllDCOfficesAction() {

        $rootDir = $this->container->getParameter('assetic.write_to');
        $data = new CsvFileIterator($rootDir . '/csv/offices.csv');

        $i = 0;
        foreach ($data as $row) {
            if ($i == 0) {$i++; continue;}

            if (empty($row[1])) {
                continue;
            }

            $office = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Office')->findOneBy(array('relatedDistricts' => $row[1], 'type' => 'DC'));
            if ($office) {
                continue;
            }

            $office = new Office();
            $office->setName($row[3]);
            $office->setType($row[0]);
            $office->setactive(1);
            $office->setRelatedDistricts(trim($row[1]));

            $district = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:District')->findOneBy(array('geocode' => trim($row[1])));
            $office->setDistrict($district);
            $office->setUpozila(null);

            $this->getDoctrine()->getEntityManager()->persist($office);

            $settings = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:OfficeSettings')->findOneBy(array('office' => $office));
            if (!$settings) {
                $ministry_settings = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:OfficeSettings')->findOneBy(array('office' => $this->getUser()->getOffice()));
                $settings = new OfficeSettings();
                $settings->setOffice($office);
                $settings->setApplicationLimitADay($ministry_settings->getApplicationLimitADay());
                $settings->setPostFeeInDistrict($ministry_settings->getPostFeeInDistrict());
                $settings->setPostFeeOutDistrict($ministry_settings->getPostFeeOutDistrict());
                $settings->setUiscApplicationReceiveFee($ministry_settings->getUiscApplicationReceiveFee());
                $settings->setUiscDeliveryDay($ministry_settings->getUiscDeliveryDay());
                $settings->setUiscDeliveryFee($ministry_settings->getUiscDeliveryFee());
                $this->getDoctrine()->getEntityManager()->persist($settings);
            }

            $DeliveryDaySettings = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('office' => $office));
            if(!$DeliveryDaySettings){
                $ministry_deliveryDays = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:DeliveryDaySettings')->findBy(array('office' => $this->getUser()->getOffice(), 'active'=>true));

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
                    $this->getDoctrine()->getEntityManager()->persist($DeliveryDaySettings);
                }
            }

            $courtFees = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:CourtFee')->findBy(array('office' => $office));
            if(!$courtFees){
                $ministry_courtFees = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:CourtFee')->findBy(array('office' => $this->getUser()->getOffice(), 'active'=>true));

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
                    $this->getDoctrine()->getEntityManager()->persist($courtFees);
                }
            }
//        additional fee

            $additionalFees = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:AdditionalFee')->findBy(array('office' => $office));
            if(!$additionalFees){
                $ministry_additionalFees = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:AdditionalFee')->findBy(array('office' => $this->getUser()->getOffice(), 'active'=>true));

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
                    $this->getDoctrine()->getEntityManager()->persist($additionalFees);
                }
            }
//        sms_setting

            $smsSettings = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:SmsSetting')->findBy(array('office' => $office));
            if(!$smsSettings){
                $ministry_smsSettings = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:SmsSetting')->findBy(array('office' => $this->getUser()->getOffice(), 'active'=>true));

                foreach($ministry_smsSettings as $ministry_smsSetting){
                    $smsSetting = new SmsSetting();
                    $smsSetting->setOffice($office);
                    $smsSetting->setActive(true);
                    $smsSetting->setApplicationType($ministry_smsSetting->getApplicationType());
                    $smsSetting->setNewApplicationMessage($ministry_smsSetting->getNewApplicationMessage());
                    $smsSetting->setDeliveryChangeMessage($ministry_smsSetting->getDeliveryChangeMessage());
                    $smsSetting->setCreatedAt( new \DateTime());
                    $smsSetting->setLocked(($ministry_smsSetting->isLocked())? true:false);
                    $this->getDoctrine()->getEntityManager()->persist($smsSetting);
                }
            }

            $this->getDoctrine()->getEntityManager()->flush();

        }

        echo 'DC Offices created';
    }

    public function assignDCAdminsToOfficesAction() {

        $rootDir = $this->container->getParameter('assetic.write_to');
        $data = new CsvFileIterator($rootDir . '/csv/dc_admin_users.csv');

        foreach ($data as $row) {
            if (!empty($row[1])) {
                $district = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:District')->findOneBy(array('geocode' => $row[1]));
                $office = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Office')->findOneBy(array('type' => 'DC', 'district' => $district));
                $user = $this->getDoctrine()->getEntityManager()->getRepository('RbsUserBundle:User')->findOneBy(array('username' => $row[2]));

            }

            if (isset($user) && isset($office)) {
                $user->setOffice($office);
                $this->getDoctrine()->getEntityManager()->persist($user);
            }
        }
        $this->getDoctrine()->getEntityManager()->flush();
        echo 'DC admin assigned to office';
    }

}
