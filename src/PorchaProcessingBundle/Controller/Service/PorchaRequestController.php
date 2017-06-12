<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\Office;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use PorchaProcessingBundle\Form\ApplicationWorkflowActionForm;
use PorchaProcessingBundle\Form\ServiceRequestType;
use PorchaProcessingBundle\Form\ServiceRequestViewType;
use PorchaProcessingBundle\Form\WorkflowActionForm;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PorchaRequestController extends BaseRequestController
{

    /**
     * @Template("@PorchaProcessing/Service/PorchaRequest/porcha_request.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_REQUEST_ENTRY")
     */
    public function porchaRequestCreateAction(Request $request,$serviceType = 'porcha_request')
    {
        if (!$this->isServiceEnabled()) {
            $this->addFlash('error', $this->get('translator')->trans('service not enabled'));

            return $this->redirect($this->generateUrl('porcha_request_list'));
        }

        $officeType = $this->getUser()->getOffice()->getType();
        $serviceRequest = new ServiceRequest();
        $this->setPorchaRequestPostalAddressFromSesion($serviceRequest);
        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();
        $this->determineWhatCanBeEditServiceRequest($serviceRequest);
        $form = $this->createForm(new ServiceRequestType($districtIds,$officeType), $serviceRequest);

        if($officeType=='UDC'){
            $thisOffice = $this->getUser()->getOffice();
            /**@var Office $thisOffice*/
            $officeSetting = $this->getServiceFee($thisOffice->getParent(), $serviceRequest);
        }else{
            $officeSetting = $this->getServiceFee($this->getUser()->getOffice(), $serviceRequest);
        }
        $applicationLimitPerDay = $officeSetting['deliveryFee']['applicationLimitPerDay'];

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $postData = $request->request->all();

                $requestCount =  $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->getServiceRequestCountByPhoneAtDate($postData, strtoupper($serviceType));

                if($requestCount >= $applicationLimitPerDay){

                    $this->addFlash('error', $this->get('translator')->trans('Application Request Count Exceed'));
                    return $this->redirect($this->generateUrl('porcha_request_list'));
                }

                $serviceRequest->setOffice($this->getUser()->getOffice());

                $this->setPorcharequestFrom($serviceRequest);

                $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')
                    ->setOfficeManager($this->get('app.service.office_manager'))
                    ->insert($serviceRequest, $officeSetting, $serviceType);

                $this->addFlash('success', $this->get('translator')->trans('Porcha Request Submitted Successfully'));

                $this->dispatch('service.request.created', new ServiceRequestEvent($serviceRequest));


                if (isset($postData['save-and-new'])) {
                    if (isset($postData['save-and-new'])) {
                        $this->savePorchaRequestPostalAddressInSesion($postData, $serviceRequest);
                    }

                    return $this->redirect($this->generateUrl('porcha_request_create'));
                }

                $request->getSession()->remove('porcha_request_applicant_info');
                return $this->redirect($this->generateUrl('porcha_request_list', array('serviceType' => $serviceType)));
            }
        }
        return array(
            'form' => $form->createView(),
            'officeSetting' => $officeSetting,
            'mode' => 'new',
        );
    }

    protected function getServiceFee(Office $office, ServiceRequest $serviceRequest, $requestType = null)
    {
        return parent::getServiceFee($office, $serviceRequest, 'PORCHA');
    }

    /**
     * @Template("@PorchaProcessing/Service/PorchaRequest/porcha_request.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_REQUEST_ENTRY")
     */
    public function porchaRequestEditAction(Request $request, ServiceRequest $serviceRequest)
    {
        $offices = $this->childOffices($this->getUser()->getOffice()->getId());
        if (!in_array($serviceRequest->getOffice()->getId(), $offices)) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        if ($serviceRequest->getStatus() == 'DELIVERED') {
            $this->addFlash('error', $this->get('translator')->trans('Already Delivered.'));
            return $this->redirect($this->generateUrl('porcha_request_list', array('serviceType' => strtolower($serviceRequest->getType()))));
        }

        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();
        $em = $this->get('doctrine.orm.entity_manager');
        $serviceRequestPorcha = $em->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findBy(array('serviceRequest' => $serviceRequest));
        $serviceRequest->setDetailEntities($serviceRequestPorcha);
        $officeType = $this->getUser()->getOffice()->getType();
        $this->determineWhatCanBeEditServiceRequest($serviceRequest);

        $form = $this->createForm(new ServiceRequestType($districtIds,$officeType), $serviceRequest);

        if($officeType=='UDC'){
            $thisOffice = $this->getUser()->getOffice();
            /**@var Office $thisOffice*/
            $officeSetting = $this->getServiceFee($thisOffice->getParent(), $serviceRequest);
        }else{

            $officeSetting = $this->getServiceFee($this->getUser()->getOffice(), $serviceRequest);
        }

        if ($request->isMethod('POST')) {
            $oldServiceRequest = clone $serviceRequest;
            $form->handleRequest($request);
            if ($form->isValid()) {

                $this->setPorcharequestFrom($serviceRequest);

                $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')
                    ->setOfficeManager($this->get('app.service.office_manager'))
                    ->update($serviceRequest, $officeSetting, $serviceRequest->getType());

                $this->addFlash('success', $this->get('translator')->trans('Porcha Request Updated Successfully'));

                $this->dispatch('service.request.updated', new ServiceRequestEvent($serviceRequest, $oldServiceRequest));

                return $this->redirect($this->generateUrl('porcha_request_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'officeSetting' => $officeSetting,
            'mode' => 'edit',
        );
    }

    public function childOffices($ownOfficeId = false) {

        $offices = array();
        if ($this->getUser()->getOffice()->getChildren()) {
            foreach ($this->getUser()->getOffice()->getChildren() as $children) {
                $offices[] = $children->getId();
            }
        }

        if ($ownOfficeId) {
            array_push($offices, $ownOfficeId);
        }
        return $offices;
    }

    /**
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_REQUEST_ENTRY")
     */
    public function porchaRequestViewAction(ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestCommonView($serviceRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function requestListAction(Request $request, $serviceType = 'porcha_request')
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $request->query->get('per-page', 25);
        $requestAll = $request->query->all();
        $data['address_print'] = false;
        $em = $this->getDoctrine();

        if (!$request->isXmlHttpRequest() && $request->query->get('address-print') == 'yes') {

            $data['address_print'] = true;
        }

        $requestAll['ff']['o.id'] = $em->getRepository('AppBundle:Office')->getChileOfficeIds($this->getUser()->getOffice());
        $requestAll['ff'] = array_filter($requestAll['ff']);

        $serviceRequest = $em->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, $serviceType, false);

        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
        $data['statistics'] = $em->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);
        $data['office'] = $this->getUser()->getOffice();
        $data['upozillas'] = $this->getOwnOfficeUpozillas();

        $template = 'PorchaProcessingBundle:Service/PorchaRequest:application_list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaRequest:application_list_sub.html.twig';
        }

        if ($request->query->get('address-print') == 'yes') {
            $template = 'PorchaProcessingBundle:Service/PorchaRequest:application_list_address_sub.html.twig';
        }

        return $this->render($template, $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function addressPrintAction(Request $request, $serviceType = 'porcha_request'){

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('address-print') == 'yes' ? 99999999 : $this->getParameter('record_per_page');
        $requestAll = $request->query->all();
        $offices = $this->getOffices();
        $serviceRequest = $this->getDoctrine()
                               ->getRepository('PorchaProcessingBundle:ServiceRequest')
                               ->getPorchaRequestAddressListPrint($requestAll, $serviceType, $offices);

        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->query->get('address-print') == 'yes') {
            $data['serviceType'] = strtoupper($serviceType);
            $template = 'PorchaProcessingBundle:Service/PorchaRequest:application_list_address_sub.html.twig';
            return $this->render($template, $data);
        }
    }



    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function requestViewAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->render('@PorchaProcessing/Service/PorchaRequest/service_request_view_modal.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'detailEntities' => $this->getDetailEntities($serviceRequest),
            'requestDetailTemplate' => '@PorchaProcessing/Service/PorchaRequest/service_request_view_modal_'.strtolower($serviceRequest->getType()).'.html.twig'
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function requestPrintAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->render('@PorchaProcessing/Service/PorchaRequest/service_request_print.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'detailEntities' => $this->getDetailEntities($serviceRequest),
            'requestDetailTemplate' => '@PorchaProcessing/Service/PorchaRequest/service_request_print_sub_'.strtolower($serviceRequest->getType()).'.html.twig'
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function requestTokenPrintAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->commonPrintAction($serviceRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function serviceRequestReceiveConfirmationAction(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->getStatus() != 'PENDING' || $this->getUser()->getOffice()->getId() != $serviceRequest->getOffice()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        sleep(2);
        /*$serviceRequest->setStatus('PROCESSING');
        $serviceRequest->setReceivedForDeliveryAt(new \DateTime());
        $serviceRequest->setReceivedForDeliveryBy($this->getUser());
        $this->getDoctrine()->getManager()->persist($serviceRequest);
        $this->getDoctrine()->getManager()->flush();*/

        return new JsonResponse(array(
            'ok'
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function serviceRequestDeliveredAction(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->getStatus() != 'PROCESSING' || $this->getUser()->getOffice()->getId() != $serviceRequest->getOffice()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        sleep(2);
        /*$serviceRequest->setStatus('DELIVERED');
        $serviceRequest->setDeliveredAt(new \DateTime());
        $serviceRequest->setDeliveredBy($this->getUser());
        $this->getDoctrine()->getManager()->persist($serviceRequest);
        $this->getDoctrine()->getManager()->flush();*/

        return new JsonResponse(array(
            'ok'
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_DELIVER_SERVICE_REQUEST")
     */
    public function deliveredServiceRequestAction(Request $request, ServiceRequest $serviceRequest)
    {
        $offices = $this->childOffices($this->getUser()->getOffice()->getId());
        if (!in_array($serviceRequest->getOffice()->getId(), $offices)) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        $serviceRequest->setStatus('DELIVERED');
        $serviceRequest->setDeliveredAt(new \DateTime());
        $serviceRequest->setDeliveredBy($this->getUser());
        $this->getDoctrine()->getManager()->persist($serviceRequest);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array('status' => 'success', 'message' => $this->get('translator')->trans('Delivered Successfully')));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function draftsAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRDraftKhatians($request->query->all());
        $data['requests']  = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('translator')->trans('Draft Khatians');
        $data['tab'] = 'DRAFT_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_draft_khatians');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    private function renderKhatianList($data)
    {
        $data['menu'] = $this->get('porcha_processing.service.khatian_application_action_menu_manager');

        if ($this->get('request')->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Service/PorchaRequest:khatian_list_sub.html.twig', $data);
        }

        $data['surveys'] = $this->get('porcha_processing.service.mouza_option_manager')->getSurveys();
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();

        return $this->render('PorchaProcessingBundle:Service/PorchaRequest:khatian_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function khatianMovetoDraftAction(KhatianLog $khatianLog)
    {
        if ($khatianLog->getEntryOperator()->getId() != $this->getUser()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("You don't have permission to move this Khatian"));
        }

        if ($khatianLog->getKhatianStatus() == 'READY_FOR_VERIFICATION' && !$khatianLog->isLocked()) {
            $khatianLog->setKhatianStatus('DRAFT');
            $this->get('porcha_processing.service.khatian_manager')->update($khatianLog);
            $this->addFlash('success',$this->get('translator')->trans('Khatian has moved to draft'));

            $this->getDoctrine()->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->updateTodayRecord();

            return $this->redirect($this->generateUrl('porcha_request_sent_khatian_list'));
        } else {
            $this->addFlash('error',$this->get('translator')->trans('Khatian can not move now'));

            return $this->redirect($this->generateUrl('porcha_request_sent_khatian_list'));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function sentKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRSentKhatianList($request->query->all());
        $data['requests']  = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('translator')->trans('Sent Khatians');
        $data['tab'] = 'SENT_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_sent_khatian_list');
        $data['hide_checkbox'] = true;

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function reAssignedKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRReAssignedKhatianList($request->query->all());
        $data['requests']  = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('porcha_processing.service.workflow_manager')->khatianListTitle($this->getUser());
        $data['tab'] = 'RE_ASSIGNED_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_re_assigned_khatian_list');
        $data['current_user'] = $this->getUser();

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_VERIFICATION")
     */
    public function verifyNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRNewKhatianListByStatus($request->query->all(), 'READY_FOR_VERIFICATION');
        $data['requests'] = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Verification)');
        $data['tab'] = 'VERIFY_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_verify_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_COMPARISON")
     */
    public function compareNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRNewKhatianListByStatus($request->query->all(), 'READY_FOR_COMPARISON');
        $data['requests'] = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Compararison');
        $data['tab'] = 'COMPARE_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_compare_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_APPROVAL")
     */
    public function approveNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRNewKhatianListByStatus($request->query->all(), 'READY_FOR_APPROVAL');
        $data['requests'] = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Applied') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Approval)');
        $data['tab'] = 'APPROVE_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('porcha_request_approve_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_APPROVAL")
     */
    public function appliedKhatiansAction(Request $request)
    {
        $this->get('session')->set('referer', $request->getRequestUri());

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRAppliedKhatians($request->query->all());
        $data['requests']  = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Filter Application & Entry List');
        $data['search_url'] = $this->generateUrl('porcha_request_applied_khatians');

        $this->get('session')->set('referer', $request->getRequestUri());

        if ($this->get('request')->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Service/PorchaRequest:applied_khatian_list_sub.html.twig', $data);
        }

        $data['surveys'] = $this->get('porcha_processing.service.mouza_option_manager')->getSurveys();
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        $data['khatian_status'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatusNames(array('DRAFT', 'ARCHIVED'));

        return $this->render('PorchaProcessingBundle:Service/PorchaRequest:applied_khatian_list.html.twig', $data);
    }

    public function moveToVerificationAction(KhatianLog $khatianLog) {

        $this->get('porcha_processing.service.workflow_manager')->srMoveToVerification($khatianLog);
        return $this->redirect($this->generateUrl('porcha_request_applied_khatians'));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_APPROVAL")
     */
    public function readyForDeliveryListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $sRequests = $this->get('porcha_processing.service.workflow_manager')->getSRReadyForDeliveryKhatians($request->query->all());
        $data['requests']  = $this->get('knp_paginator')->paginate($sRequests, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Ready to send');
        $data['search_url'] = $this->generateUrl('porcha_request_ready_for_delivery');

        $this->get('session')->set('referer', $request->getRequestUri());

        if ($this->get('request')->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Service/PorchaRequest:khatian_delivery_list_sub.html.twig', $data);
        }

        $data['surveys'] = $this->get('porcha_processing.service.mouza_option_manager')->getSurveys();
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();

        return $this->render('PorchaProcessingBundle:Service/PorchaRequest:khatian_delivery_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function workflowKhatianPagesAction(Request $request, KhatianLog $khatianLog)
    {
//        if (!$this->get('porcha_processing.service.workflow_manager')->isWorkflowPageViewableByKhatianLog($khatianLog, 'APP')) {
//            throw $this->createAccessDeniedException($this->get('translator')->trans('ENTRY_RESTRICTED'));
//        }

        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatianManager->lock($khatianLog, $this->getUser());

        $khatianPageEntities = $khatianManager->getKhatianPages($khatianLog->getKhatianVersion());
        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);

        $entitled = $this->isGranted('ROLE_KHATIAN_VERIFICATION') || $this->isGranted('ROLE_KHATIAN_COMPARISON') || $this->isGranted('ROLE_KHATIAN_APPROVAL');
        $workflowForm = $this->createForm(new WorkflowActionForm($entitled),
            array('correctionMessages' => $this->get('porcha_processing.service.khatian_correction_log_manager')->findAllByKhatianPages($khatianPageEntities))
        );

        return $this->render(
            '@PorchaProcessing/Service/PorchaRequest/workflow_view_khatian.html.twig', array(
                'action_url'   => $this->generateUrl('porcha_request_workflow_action', array('id' => $khatianLog->getId())),
                'workflowForm' => $workflowForm->createView(),
                'khatianPages' => $khatianPages,
                'khatian'      => $khatian,
                'khatianLog'   => $khatianLog,
                'pagination'   => $pagination
            )
        );
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function workflowActionAction(Request $request, KhatianLog $khatianLog)
    {
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        $workflowAction = $request->request->get('porcha_processing_workflow_action[workflowAction]', 'ACTION_BACK', true);
        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatianPageEntities = $khatianManager->getKhatianPages($khatian->getLastVersion());

        $correctionLogs = $this->get('porcha_processing.service.khatian_correction_log_manager')->findAllByKhatianPages(
            $khatianPageEntities
        );
        $workflowForm = $this->createForm(new ApplicationWorkflowActionForm(),
            array('correctionMessages' => $correctionLogs)
        );
        $workflowForm->handleRequest($request);
        if ($workflowForm->isValid()) {

            $this->get('porcha_processing.service.workflow_manager')->srWorkflowAction($khatianLog, $workflowAction, $correctionLogs, $this->getUser());
            if ($workflowAction == 'ACTION_FORWARD') {

                $label = $this->get('translator')->trans('Khatian Forwarded');
                if ($khatian->isArchived()) {
                    $label = ($khatian->isBatch()) ?  $this->get('translator')->trans('Khatian Archived') : $this->get('translator')->trans('Khatian sent for delivery');
                }
                $this->addFlash('success', $this->get('translator')->trans($label));
            } else {
                $this->addFlash('success',$this->get('translator')->trans('Khatian sent back to entry operator'));
            }

            $this->getDoctrine()->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->updateTodayRecord();

            return $this->redirect($this->get('session')->get('referer', 'service_request_khatian_re_assigned_khatian_list'));
        }
        return $this->redirect($this->get('session')->get('referer', $this->generateUrl('porcha_request_re_assigned_khatian_list')));
    }

    public function findApplicationKhatianAction(Request $request, ServiceRequestPorcha $serviceRequestPorcha) {

        $result = $this->get('porcha_processing.service.khatian_manager')->khatianVolumes($serviceRequestPorcha);

        switch ($result['status']) {

            case 'ENTRY_RESTRICTED':
                $this->addFlash('error',$this->get('translator')->trans('ENTRY_RESTRICTED'));
                return $this->redirect($this->generateUrl('porcha_request_applied_khatians'));
            case 'NO_TEMPLATE':
            case 'NO_VOLUME':
            case 'UNAPPROVED_VOLUME':
                if ($result['status'] == 'NO_TEMPLATE') {
                    $this->addFlash('error',$this->get('translator')->trans('khatian template not found'));
                }
                if ($result['status'] == 'UNAPPROVED_VOLUME') {
                    $this->addFlash('error',$this->get('translator')->trans('volume approval needed'));
                }
                return $this->render('PorchaProcessingBundle:Service/PorchaRequest:matching_volumes.html.twig', array(
                    'volumes' => $result['volumes']
                ));
            case 'SUCCESS':
                $khatianLog = $result['khatianLog'];
                $khatianFirstPage = $this->get('porcha_processing.service.khatian_manager')->getKhatianFirstPageByKhatianLog($khatianLog);
                return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianFirstPage->getId())).'?khatian_no='.$serviceRequestPorcha->getKhatianNo().'&sr='.$serviceRequestPorcha->getServiceRequest()->getId());
        }
    }

    public function vrrSaveKhatianAction(Request $request){

        if (!$request->headers->has('X-ApiKey')) {
            return new Response('Required API key is missing.', 400);
        }

        if ($request->headers->get('X-ApiKey') != '123') {
            return new Response('Unauthorized access.', 401);
        }
        $data = $request->request->all();
        /* file_put_contents(
               $this->get('kernel')->getRootDir() . '/../request_data.txt', implode(", ", $data)
          );*/
        
        $citizen = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')
            ->setOfficeManager($this->get('app.service.office_manager'))
            ->createService($data);

        $ret = array();

            $ret[] = array(
                'id' => $citizen->getId(),
                'name' => $citizen->getName(),
                'house' => $citizen->getHouseNo(),
                'road' => $citizen->getArea(),
                'postal' => $citizen->getPostalCode(),
                'area' => $citizen->getArea(),
                'district' => $citizen->getDistrict()?$citizen->getDistrict()->getName():'',
                'upozila' => $citizen->getUpozila()?$citizen->getUpozila()->getName():'',
                'phone' => $citizen->getContactNumber(),
                'createdDate' => $citizen->getCreatedAt()->format('jS F Y'),
                'delivaryDate' => $citizen->getEstimateDeliveryAt()->format('jS F Y'),
                'type' => $citizen->getType(),
                'courtFee' => $citizen->getCourtFee(),
                'delivaryFee' => $citizen->getDeliveryFee(),
                'totalFee' => $citizen->getTotalFee(),
            );

        $response = new JsonResponse($ret, 201, [
            'Content-type' => 'application/json'
        ]);

        return $response;

    }

    public function vrrSaveKhatianCorrectionAction(Request $request){

        if (!$request->headers->has('X-ApiKey')) {
            return new Response('Required API key is missing.', 400);
        }

        if ($request->headers->get('X-ApiKey') != '123') {
            return new Response('Unauthorized access.', 401);
        }
        $data = $request->request->all();
        /* file_put_contents(
             $this->get('kernel')->getRootDir() . '/../request_data.txt', implode(", ", $data)
         );*/
        $repositoryServiceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha');
        $citizen  = $repositoryServiceRequest->createCorrectionService($data);
        $response = new Response(json_encode($citizen), 201, [
            'Content-type' => 'application/json'
        ]);

        return $response;
    }
}
