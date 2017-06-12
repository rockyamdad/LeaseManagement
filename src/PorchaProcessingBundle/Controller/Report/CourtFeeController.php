<?php

namespace PorchaProcessingBundle\Controller\Report;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

class CourtFeeController extends Controller
{

    /**
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_MANAGE,ROLE_PORCHA_REQUEST_MANAGE")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $this->getParameter('record_per_page');
        $requestAll = $request->query->all();
        $requestAll['ff']['o.id'] = $this->getUser()->getOffice()->getId();

        if (!$request->isXmlHttpRequest()) {
            $requestAll['ff']['sr.createdAt'] = !empty($requestAll['ff']['sr.createdAt']) ? $requestAll['ff']['sr.createdAt'] : date('Y-m-d');
        }
        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, 'PORCHA_REQUEST');
        $data['sl'] = ($page - 1) * $perPage + 1;

        if (isset($requestAll['view-type']) && $requestAll['view-type'] == 'print') {
            $template = 'PorchaProcessingBundle:Report/CourtFee:index_print.html.twig';
            $data['serviceRequest']  = $serviceRequest->getResult();
        } else {
            $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
            $data['serviceType'] = 'PORCHA_REQUEST';
            $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, 'PORCHA_REQUEST');

            $template = 'PorchaProcessingBundle:Report/CourtFee:index.html.twig';
        }


        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Report/CourtFee:list_sub.html.twig';
        }

        return $this->render($template, $data);
    }
}
