<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\Office;
use JMS\SecurityExtraBundle\Annotation as JMS;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use PorchaProcessingBundle\Form\CorrectionRequestAcLandType;
use PorchaProcessingBundle\Form\CorrectionRequestType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PorchaCorrectionRequestController extends BaseRequestController
{

    /**
     * @Template("@PorchaProcessing/Service/PorchaCorrectionRequest/porcha_correction_request.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_CORRECTION_REQUEST_ENTRY")
     */
    public function createAction(Request $request)
    {

        $serviceType = 'porcha_correction_request';
        $officeType     = $this->getUser()->getOffice()->getType();

        $serviceRequest = new ServiceRequest();
        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();

        $form = $this->createForm(new CorrectionRequestType($districtIds, $officeType), $serviceRequest);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            if ($form->isValid()) {

                $this->save($serviceRequest,$officeType);

                $this->addFlash('success', $this->get('translator')->trans('Correction Request Submitted Successfully'));

                $this->dispatch('correction.request.created', new ServiceRequestEvent($serviceRequest));

                return $this->redirect($this->generateUrl('porcha_correction_request_list'));
            }
        }

        return array(
            'form'          => $form->createView(),
            'mode'          => 'new'
        );
    }

    /**
     * @Template("@PorchaProcessing/Service/PorchaCorrectionRequest/porcha_correction_request_ac_land.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_CORRECTION_REQUEST_AC_LAND_ENTRY")
     */
    public function CreatePorchCorrectionFormAcLandAction(Request $request)
    {

        $serviceType = 'porcha_correction_request';
        $officeType     = $this->getUser()->getOffice()->getType();

        $serviceRequest = new ServiceRequest();
        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();

        $form = $this->createForm(new CorrectionRequestAcLandType($districtIds, $officeType), $serviceRequest);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);
            if ($form->isValid()) {

                $this->save($serviceRequest,$officeType);

                $this->addFlash('success', $this->get('translator')->trans('Correction Request Submitted Successfully'));

                $this->dispatch('correction.request.created', new ServiceRequestEvent($serviceRequest));

                return $this->redirect($this->generateUrl('porcha_correction_request_ac_land_list'));
            }
        }

        return array(
            'form'          => $form->createView(),
            'mode'          => 'new'
        );
    }

    private function save(ServiceRequest $serviceRequest,$officeType)
    {
        $em = $this->getDoctrine()->getManager();

        if($officeType == 'DC') {

            $serviceRequest->setRequestFrom('DIRECT');
            $serviceRequest->setDeliveryMethod('DIRECT');

        } elseif($officeType == 'UDC') {

            $serviceRequest->setRequestFrom('UDC');
            $serviceRequest->setDeliveryMethod('UDC');

        } elseif($officeType == 'AC_LAND') {

            $serviceRequest->setRequestFrom('AC_LAND');
            $serviceRequest->setDeliveryMethod('AC_LAND');
        }

        $serviceRequest->setCreatedAt(new \DateTime());
        $serviceRequest->setStatus('PENDING');
        $serviceRequest->setType('PORCHA_CORRECTION_REQUEST');
        $serviceRequest->setUrgency('NORMAL');
        $serviceRequest->setOffice($this->getUser()->getOffice());

        $em->persist($serviceRequest);
        $em->flush();

        /** @var ServiceRequestPorcha $porcha */
        foreach ($serviceRequest->getDetailEntities() as $porcha) {
            $porcha->setServiceRequest($serviceRequest);
            $em->persist($porcha);
        }
        $em->flush();
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_CORRECTION_REQUEST_MANAGE")
     */
    public function listAction(Request $request)
    {

        $serviceType = 'porcha_correction_request';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $request->query->get('per-page', 25);
        $requestAll = $request->query->all();

        $offices = $this->getOffices();

        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, $serviceType, false, $offices);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
        $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);
        $data['upozillas'] = $this->getOwnOfficeUpozillas();
        $data['this_office'] = $this->getUser()->getOffice();

        $template = 'PorchaProcessingBundle:Service/PorchaCorrectionRequest:list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaCorrectionRequest:list_sub.html.twig';
        }
        return $this->render($template, $data);
    } 
    /**
     * @JMS\Secure(roles="ROLE_PORCHA_CORRECTION_REQUEST_AC_LAND_MANAGE")
     */
    public function acLandPorchaCorrectionListAction(Request $request)
    {

        $serviceType = 'porcha_correction_request';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $request->query->get('per-page', 25);
        $requestAll = $request->query->all();

        $offices = $this->getOffices();

        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, $serviceType, false, $offices);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
     //   $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);
        $data['upozillas'] = $this->getOwnOfficeUpozillas();
        $data['this_office'] = $this->getUser()->getOffice();

        $template = 'PorchaProcessingBundle:Service/PorchaCorrectionRequest:ac_land_porcha_correction_list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaCorrectionRequest:ac_land_porcha_correction_list_sub.html.twig';
        }
        return $this->render($template, $data);
    }
}