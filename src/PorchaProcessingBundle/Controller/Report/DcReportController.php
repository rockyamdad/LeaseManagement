<?php

namespace PorchaProcessingBundle\Controller\Report;

use PorchaProcessingBundle\Form\Report\UdcReceivedReportSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

class DcReportController extends Controller
{

    /**
     * @JMS\Secure(roles="ROLE_VIEW_DC_RELATED_REPORTS")
     */
    public function indexAction(Request $request)
    {

        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        if(isset($requestAll['print'])){
            $perPage = 100000;
        };

        $serviceRequest = $this->get('porcha_processing.service.report_manager')->getVolumeWiseReportList($requestAll);
        $data['VolumeWiseEntries']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Report/Dc:volume_wise_list_sub.html.twig', $data);
        }

        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();

        return $this->render('PorchaProcessingBundle:Report/Dc:volume_wise_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_DC_RELATED_REPORTS")
     */
    public function listKhatianDeliveryRegisterAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        if(isset($requestAll['print'])){
            $perPage = 100000;
        };

        $serviceRequest = $this->get('porcha_processing.service.report_manager')->getDeliveryRegisterReportList($requestAll,'PORCHA_REQUEST');
        $data['deliveryKhatianRegisters']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Report/Dc:delivery_khatian_register_list_sub.html.twig', $data);
        }
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));

        return $this->render('PorchaProcessingBundle:Report/Dc:delivery_khatian_register_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_DC_RELATED_REPORTS")
     */
    public function listMouzaMapDeliveryRegisterAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        if(isset($requestAll['print'])){
            $perPage = 100000;
        };

        $serviceRequest = $this->get('porcha_processing.service.report_manager')
                               ->getDeliveryRegisterReportList($requestAll,'MOUZA_MAP');
        $data['deliveryMouzaMapRegisters']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Report/Dc:delivery_mouza_map_register_list_sub.html.twig', $data);
        }
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));

        return $this->render('PorchaProcessingBundle:Report/Dc:delivery_mouza_map_register_list.html.twig', $data);
    }


    /**
     * @JMS\Secure(roles="ROLE_VIEW_DC_RELATED_REPORTS")
     */
    public function listCourtFeeRegisterAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        if(isset($requestAll['print'])){
            $perPage = 100000;
        };

        $serviceRequest = $this->get('porcha_processing.service.report_manager')
                               ->getCourtFeeRegisterReportList($requestAll);
        $data['courtFeeRegisters']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Report/Dc:court_fee_register_list_sub.html.twig', $data);
        }
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));

        return $this->render('PorchaProcessingBundle:Report/Dc:court_fee_register_list.html.twig', $data);
    }
}
