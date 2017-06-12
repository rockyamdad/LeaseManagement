<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\Office;
use JMS\SecurityExtraBundle\Annotation as JMS;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use PorchaProcessingBundle\Form\MouzaMapRequestType;
use PorchaProcessingBundle\Form\MouzaMapRequestViewType;
use PorchaProcessingBundle\Form\ServiceRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MouzaMapRequestController extends BaseRequestController
{
    /**
     * @Template("@PorchaProcessing/Service/MouzaMap/create.html.twig")
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_ENTRY")
     */
    public function createAction(Request $request)
    {
        if (!$this->isServiceEnabled()) {
            $this->addFlash('error', $this->get('translator')->trans('service not enabled'));

            return $this->redirect($this->generateUrl('mouza_list'));
        }
        
        $serviceType = 'MOUZA_MAP';
        $officeType = $this->getUser()->getOffice()->getType();
        $serviceRequest = new ServiceRequest();
        $this->setPorchaRequestPostalAddressFromSesion($serviceRequest);
        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();
        $this->determineWhatCanBeEditServiceRequest($serviceRequest);
        $form = $this->createForm(new MouzaMapRequestType($districtIds, $officeType), $serviceRequest);

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
                    return $this->redirect($this->generateUrl('mouza_map_request_create'));
                }

                $serviceRequest->setOffice($this->getUser()->getOffice());

                $this->setPorcharequestFrom($serviceRequest);
                $date = $this->get('app.service.office_manager')->applicationDeliveryDate(date('Y-m-d'), 3);
                $serviceRequest->setEstimateDeliveryAt(new \DateTime($date));

                $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')
                    ->setOfficeManager($this->get('app.service.office_manager'))
                    ->insertMouzaMap($serviceRequest, $officeSetting);

                $this->addFlash('success', $this->get('translator')->trans('Mouza Map Request Submitted Successfully'));

                $this->dispatch('service.request.created', new ServiceRequestEvent($serviceRequest));

                if (isset($postData['save-and-new'])) {
                    if (isset($postData['save-and-new'])) {
                        $this->savePorchaRequestPostalAddressInSesion($postData, $serviceRequest);
                    }

                    return $this->redirect($this->generateUrl('mouza_map_request_create'));
                }

                $request->getSession()->remove('porcha_request_applicant_info');
                return $this->redirect($this->generateUrl('mouza_map_request_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'officeSetting' => $officeSetting,
            'mode' => 'new'
        );
    }

    protected function getServiceFee(Office $office, ServiceRequest $serviceRequest, $requestType = null)
    {
        return parent::getServiceFee($office, $serviceRequest, 'MOUZA_MAP');
    }

    /**
     * @Template("@PorchaProcessing/Service/MouzaMap/create.html.twig")
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_ENTRY")
     */
    public function editAction(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->getOffice()->getId() !== $this->getUser()->getOffice()->getId()) {
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
        $form = $this->createForm(new MouzaMapRequestType($districtIds, $officeType), $serviceRequest);

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
                    ->updateMouzaMap($serviceRequest, $officeSetting);

                $this->addFlash('success', $this->get('translator')->trans('Mouza Map Request Updated Successfully'));

                $this->dispatch('service.request.updated', new ServiceRequestEvent($serviceRequest, $oldServiceRequest));

                return $this->redirect($this->generateUrl('mouza_map_request_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'officeSetting' => $officeSetting,
            'mode' => 'edit'
        );
    }

    /**
     * @Template("@PorchaProcessing/Service/MouzaMap/view.html.twig")
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_ENTRY")
     */
    public function viewAction(ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestCommonView($serviceRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_MANAGE")
     */
    public function listAction(Request $request)
    {
        $serviceType = 'mouza_map';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $request->query->get('per-page', 25);
        $requestAll = $request->query->all();

        $requestAll['ff']['o.id'] = $this->getDoctrine()->getRepository('AppBundle:Office')->getChileOfficeIds($this->getUser()->getOffice());
        $requestAll['ff'] = array_filter($requestAll['ff']);
        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, $serviceType);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
        $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);
        $data['upozillas'] = $this->getOwnOfficeUpozillas();
        $data['this_office'] = $this->getUser()->getOffice();

        $template = 'PorchaProcessingBundle:Service/MouzaMap:list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/MouzaMap:list_sub.html.twig';
        }
        return $this->render($template, $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_MANAGE")
     */
    public function requestViewAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->render('@PorchaProcessing/Service/MouzaMap/service_request_view_modal.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'detailEntities' => $this->getDetailEntities($serviceRequest),
            'requestDetailTemplate' => '@PorchaProcessing/Service/MouzaMap/service_request_view_modal_'.strtolower($serviceRequest->getType()).'.html.twig'
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_MANAGE")
     */
    public function requestPrintAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->render('@PorchaProcessing/Service/MouzaMap/service_request_print.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'porcha' => $this->getDetailEntities($serviceRequest),
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_MAP_REQUEST_MANAGE")
     */
    public function requestTokenPrintAction(Request $request, ServiceRequest $serviceRequest)
    {
        return $this->commonPrintAction($serviceRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_START_SERVICE_REQUEST, ROLE_COMPLETE_SERVICE_REQUEST, ROLE_DELIVER_SERVICE_REQUEST")
     */
    public function updateDeliveryStatusAction(ServiceRequest $serviceRequest)
    {
        $message = '';
        if ($serviceRequest->getStatus() == 'PENDING' && $this->isGranted('ROLE_START_SERVICE_REQUEST')) {
            $message = 'Mouza map is now ready for processing';
            $serviceRequest->setStatus('PROCESSING');
        } else if ($serviceRequest->getStatus() == 'PROCESSING' && $this->isGranted('ROLE_COMPLETE_SERVICE_REQUEST')) {
            $message = 'Mouza map is now ready for delivery';
            $serviceRequest->setStatus('READY_FOR_DELIVERY');
        } else if ($serviceRequest->getStatus() == 'READY_FOR_DELIVERY' && $this->isGranted('ROLE_DELIVER_SERVICE_REQUEST')) {
            $message = 'Mouza map delivered';
            $serviceRequest->setDeliveredBy($this->getUser());
            $serviceRequest->setStatus('DELIVERED');
        }
        $this->getDoctrine()->getManager()->persist($serviceRequest);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array('status' => 'success', 'message' => $this->get('translator')->trans($message)));
    }

    public function saveMouzaAction(Request $request){

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
            ->createServiceForMouza($data);

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
}
