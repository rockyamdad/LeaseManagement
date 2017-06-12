<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\ACLandDocument;
use AppBundle\Entity\Document;
use AppBundle\Entity\Office;
use JMS\SecurityExtraBundle\Annotation as JMS;
use PorchaProcessingBundle\Entity\PorchaCopyRequest;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use PorchaProcessingBundle\Form\CorrectionRequestAcLandType;
use PorchaProcessingBundle\Form\CorrectionRequestType;

use PorchaProcessingBundle\Form\PorchaCopyRequestType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PorchaCopyRequestController extends BaseRequestController
{

    /**
     * @Template("@PorchaProcessing/Service/PorchaCopyRequest/porcha_copy_request.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_COPY_REQUEST_ENTRY")
     */
    public function createAction(Request $request)
    {
        $copyRequest = new PorchaCopyRequest();
        
        $form = $this->createForm(new PorchaCopyRequestType(), $copyRequest);

        if ($request->isMethod('POST')) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $hh */
            $form->handleRequest($request);
            if ($form->isValid()) {

                $files = $request->files->get('porchaprocessingbundle_porchacopyrequest[documents]', null, true);

                $this->get('app.service.ac_land_manager')->saveAcLandDocument($files,$copyRequest);

                $this->get('app.service.ac_land_manager')->saveCopyRequestAcLand($copyRequest);
                $this->addFlash('success', $this->get('translator')->trans('Correction Request Submitted Successfully'));


                return $this->redirect($this->generateUrl('porcha_copy_request_list'));
            }
        }
        return array(
            'form'          => $form->createView(),
            'mode'          => 'new'
        );
    }
    
    /**
     * @Template("@PorchaProcessing/Service/PorchaCopyRequest/porcha_copy_request_dc.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_COPY_REQUEST_ENTRY_DC")
     */
    public function createDcAction(Request $request)
    {
        $copyRequest = new PorchaCopyRequest();

        $form = $this->createForm(new PorchaCopyRequestType($this->getUser()->getOffice()), $copyRequest);

        if ($request->isMethod('POST')) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $hh */
            $form->handleRequest($request);
            if ($form->isValid()) {
                
                $files = $request->files->get('porchaprocessingbundle_porchacopyrequest[documents]', null, true);
                
                $this->get('app.service.ac_land_manager')->saveAcLandDocument($files,$copyRequest);
                $this->get('app.service.ac_land_manager')->saveCopyRequestDc($copyRequest);
                
                $this->addFlash('success', $this->get('translator')->trans('Correction Request Submitted Successfully'));


                return $this->redirect($this->generateUrl('porcha_copy_request_list'));
            }
        }

        return array(
            'form'          => $form->createView(),
            'mode'          => 'new'
        );
    } 
    /**
     * @Template("@PorchaProcessing/Service/PorchaCopyRequest/porcha_copy_response_reply.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_COPY_REQUEST_ENTRY_DC,ROLE_PORCHA_COPY_REQUEST_ENTRY")
     */
    public function replyAction(Request $request , PorchaCopyRequest $copyRequest)
    {
        
        $form = $this->createForm(new PorchaCopyRequestType($this->getUser()->getOffice(),$copyRequest->getId()), $copyRequest);
        
        $this->viewablePorchaCopyRequestEntry($copyRequest);

        if ($request->isMethod('POST')) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $hh */
            $form->handleRequest($request);
            if ($form->isValid()) {
                $files = $request->files->get('porchaprocessingbundle_porchacopyrequest[documents]', null, true);

                $this->get('app.service.ac_land_manager')->saveAcLandDocument($files,$copyRequest);
                $this->get('app.service.ac_land_manager')->replyCopyRequest($copyRequest);

                $this->addFlash('success', $this->get('translator')->trans('Correction Request Submitted Successfully'));


                return $this->redirect($this->generateUrl('porcha_copy_request_list'));
            }
        }

        return array(
            'form'          => $form->createView(),
            'entity'          => $copyRequest,
            'mode'          => 'edit'
        );
    }
    
    /**
     * @Template("@PorchaProcessing/Service/PorchaCopyRequest/porcha_copy_view.html.twig")
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_ENTRY,ROLE_PORCHA_COPY_REQUEST_ENTRY_DC,ROLE_PORCHA_COPY_REQUEST_MANAGE")
     */
    public function viewAction(PorchaCopyRequest $copyRequest)
    {
        foreach ($copyRequest->getDocuments() as $document){
            $office = $this->getDoctrine()->getRepository('RbsUserBundle:User')->find($document->getCreatedBy());
            $oficesType[] =$office->getOffice()->getType();
        }

        return array(
            'form'          => $copyRequest,
            'officeType'    => $oficesType
        );
    }
    
    private function viewablePorchaCopyRequestEntry($copyRequest) {

        $this->get('app.service.ac_land_manager')->viewableCopyRequest($copyRequest);
    }

    /**
     * @JMS\Secure(roles="ROLE_PORCHA_COPY_REQUEST_MANAGE_DC , ROLE_PORCHA_COPY_REQUEST_MANAGE")
     */
    public function requestListAction(Request $request)
    {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);
        $requestAll = $request->query->all();
        $office = $this->getUser()->getOffice()->getId();
        $officeType = $this->getUser()->getOffice()->getType();
        $data['officeType'] = $officeType ;
        $data['tab'] = '1';
        $porchaCopyRequest = $this->getDoctrine()
                                  ->getRepository('PorchaProcessingBundle:PorchaCopyRequest')
                                  ->getAllPorchaCopyRequest($requestAll,$office);

        $data['copyRequests']  = $this->get('knp_paginator')->paginate($porchaCopyRequest, $page, $perPage);

        $template = 'PorchaProcessingBundle:Service/PorchaCopyRequest:list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaCopyRequest:list_sub.html.twig';
        }
        return $this->render($template, $data);
    }
    
    /**
     * @JMS\Secure(roles="ROLE_PORCHA_COPY_REQUEST_MANAGE_DC , ROLE_PORCHA_COPY_REQUEST_MANAGE")
     */
    public function responseListAction(Request $request)
    {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);
        $requestAll = $request->query->all();
        $office = $this->getUser()->getOffice()->getId();
        $officeType = $this->getUser()->getOffice()->getType();
        $data['officeType'] = $officeType ;
        $data['tab'] = 2;
        $porchaCopyRequest = $this->getDoctrine()
                                  ->getRepository('PorchaProcessingBundle:PorchaCopyRequest')
                                  ->getAllPorchaCopyResponse($requestAll,$office);

        $data['copyRequests']  = $this->get('knp_paginator')->paginate($porchaCopyRequest, $page, $perPage);

        $template = 'PorchaProcessingBundle:Service/PorchaCopyRequest:response_list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaCopyRequest:response_list_sub.html.twig';
        }
        return $this->render($template, $data);
    }

  
    
}