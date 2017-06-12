<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\Office;
use JMS\SecurityExtraBundle\Annotation as JMS;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use PorchaProcessingBundle\Form\ServiceRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InformationSlipRequestController extends BaseRequestController
{
    /**
     * @Template("@PorchaProcessing/Service/InformationSlip/application_create.html.twig")
     * @JMS\Secure(roles="ROLE_INFORMATION_SLIP_REQUEST_ENTRY")
     */
    public function createAction(Request $request, $serviceType = 'information_slip')
    {
        if (!$this->isServiceEnabled()) {
            $this->addFlash('error', $this->get('translator')->trans('service not enabled'));

            return $this->redirect($this->generateUrl('information_slip_list'));
        }
        
        $officeType = $this->getUser()->getOffice()->getType();
        $serviceRequest = new ServiceRequest();
        $serviceRequest->setType(strtoupper($serviceType));
        $this->setPorchaRequestPostalAddressFromSesion($serviceRequest);
        $this->determineWhatCanBeEditServiceRequest($serviceRequest);

        $form = $this->createForm(new ServiceRequestType(null, $officeType), $serviceRequest);

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
                    return $this->redirect($this->generateUrl('information_slip_create'));
                }

                $serviceRequest->setOffice($this->getUser()->getOffice());
                $this->setPorcharequestFrom($serviceRequest);

                $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')
                    ->setOfficeManager($this->get('app.service.office_manager'))
                    ->create($serviceRequest, $officeSetting);

                $this->addFlash('success', $this->get('translator')->trans('Information Slip Request Submitted Successfully'));

                $this->dispatch('service.request.created', new ServiceRequestEvent($serviceRequest));

                if (isset($postData['save-and-new'])) {
                    if (isset($postData['save-and-new'])) {
                        $this->savePorchaRequestPostalAddressInSesion($postData, $serviceRequest);
                    }

                    return $this->redirect($this->generateUrl('information_slip_create'));
                }

                $request->getSession()->remove('porcha_request_applicant_info');

                return $this->redirect($this->generateUrl('information_slip_list', array('serviceType' => $serviceType)));
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
        return parent::getSingleServiceFee($office, $serviceRequest, 'INFORMATION_SLIP');
    }

    /**
     * @Template("@PorchaProcessing/Service/InformationSlip/application_create.html.twig")
     * @JMS\Secure(roles="ROLE_INFORMATION_SLIP_REQUEST_ENTRY")
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

        $this->determineWhatCanBeEditServiceRequest($serviceRequest);
        $officeType = $this->getUser()->getOffice()->getType();
        $this->setPorchaRequestPostalAddressFromSesion($serviceRequest);
        $form = $this->createForm(new ServiceRequestType(null, $officeType), $serviceRequest);

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

                $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->save($serviceRequest, $officeSetting);

                $this->addFlash('success', $this->get('translator')->trans('Information Slip Request Updated Successfully'));

                $this->dispatch('service.request.updated', new ServiceRequestEvent($serviceRequest, $oldServiceRequest));

                $postData = $request->request->all();
                if (isset($postData['save-and-new'])) {
                    if (isset($postData['save-and-new'])) {
                        $this->savePorchaRequestPostalAddressInSesion($postData, $serviceRequest);
                    }

                    return $this->redirect($this->generateUrl('information_slip_create'));
                }

                $request->getSession()->remove('porcha_request_applicant_info');

                return $this->redirect($this->generateUrl('information_slip_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'officeSetting' => $officeSetting,
            'mode' => 'edit'
        );
    }

    /**
     * @JMS\Secure(roles="ROLE_INFORMATION_SLIP_REQUEST_MANAGE")
     */
    public function listAction(Request $request, $serviceType = 'porcha_request')
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $request->query->get('per-page', 25);
        $requestAll = $request->query->all();

        $requestAll['ff']['o.id'] = $this->getDoctrine()->getRepository('AppBundle:Office')->getChileOfficeIds($this->getUser()->getOffice());
        $requestAll['ff'] = array_filter($requestAll['ff']);
        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getList($requestAll, $serviceType);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
        $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);
        $data['this_office'] = $this->getUser()->getOffice();
        
        $template = 'PorchaProcessingBundle:Service/InformationSlip:application_list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/InformationSlip:application_list_sub.html.twig';
        }

        return $this->render($template, $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_INFORMATION_SLIP_REQUEST_MANAGE")
     */
    public function viewAction(ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestCommonView($serviceRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_INFORMATION_SLIP_REQUEST_MANAGE")
     */
    public function printAction(ServiceRequest $serviceRequest)
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
            $message = 'Information slip is now ready for processing';
            $serviceRequest->setStatus('PROCESSING');
        } else if ($serviceRequest->getStatus() == 'PROCESSING' && $this->isGranted('ROLE_COMPLETE_SERVICE_REQUEST')) {
            $message = 'Information slip is now ready for delivery';
            $serviceRequest->setStatus('READY_FOR_DELIVERY');
        } else if ($serviceRequest->getStatus() == 'READY_FOR_DELIVERY' && $this->isGranted('ROLE_DELIVER_SERVICE_REQUEST')) {
            $message = 'Information slip delivered';
            $serviceRequest->setDeliveredBy($this->getUser());
            $serviceRequest->setStatus('DELIVERED');
        }
        $this->getDoctrine()->getManager()->persist($serviceRequest);
        $this->getDoctrine()->getManager()->flush();
        
        return new JsonResponse(array('status' => 'success', 'message' => $this->get('translator')->trans($message)));
    }

    public function saveInformationApplicationAction(Request $request){

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
        
        $citizen = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')
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

}
