<?php

namespace PorchaProcessingBundle\Controller\Report;

use PorchaProcessingBundle\Form\Report\UdcReceivedReportSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

class UdcReportController extends Controller
{

    /**
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_MANAGE,ROLE_VIEW_UDC_RELATED_REPORTS,ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function indexAction(Request $request, $reportType)
    {

        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        $data['reportType'] = $reportType;
        if(isset($requestAll['print'])){
            $perPage = 100000;
        };

        $serviceRequest = $this->get('porcha_processing.service.report_manager')->getServiceRequestReportList($requestAll, $reportType);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            $serviceType = $request->query->get('ff') ? $request->query->get('ff')['sr.type'] : 'PORCHA_REQUEST';
            return $this->render($this->renderServiceRequestTemplate($serviceType), $data);
        }
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        return $this->render('PorchaProcessingBundle:Report/Udc:received_list.html.twig', $data);
    }

    private function renderServiceRequestTemplate($serviceType){
        switch(strtoupper($serviceType)){
            case 'PORCHA_REQUEST':
            case 'MOUZA_MAP':
                return 'PorchaProcessingBundle:Report/Udc:received_porcha_list_sub.html.twig';
            case 'CASE_COPY':
                return 'PorchaProcessingBundle:Report/Udc:received_case_copy_list_sub.html.twig';
            case 'INFORMATION_SLIP':
                return 'PorchaProcessingBundle:Report/Udc:received_list_sub.html.twig';
        }
    }

    public function udcReceivedReportSearchForm($request)
    {
        $districtIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();

        $form = new UdcReceivedReportSearchType($districtIds);
        $data = $request->get($form->getName());
        return array($form, $data);
    }

    public function testAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');

        list($form, $data) = $this->udcReceivedReportSearchForm($request);

        $form = $this->createForm($form);
        $form->submit($data);

        if(!empty($data)){
                $serviceRequest = $this->getDoctrine()
                                       ->getRepository('PorchaProcessingBundle:ServiceRequest')
                                       ->getServiceRequestReportList($data);

        }else {
            $serviceRequest = array();
        }
        return $this->render('PorchaProcessingBundle:Report/Udc:received_list.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'form' => $form->createView(),
        ));
    }

}
