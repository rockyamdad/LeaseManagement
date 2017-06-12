<?php

namespace PorchaProcessingBundle\Controller\Report;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TopSheetController extends Controller
{
    public function indexAction(Request $request)
    {
        $officeDistrict = $this->getUser()->getOffice() ? $this->getUser()->getOffice()->getDistrict() : null;
        //$data['upozillas'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Upozila')->findBy(array('district' => $officeDistrict));
        $data['upozillas'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Upozila')->findAll();
        $data['suveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();

        return $this->render('PorchaProcessingBundle:Report/TopSheet:index.html.twig', $data);
    }

    public function listAction(Request $request)
    {
        $requestAll = $request->query->all();

        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, 'PORCHA_REQUEST');
        $data['serviceRequestByMouza'] = $this->prepareGroupByMouza($serviceRequest->getResult());

        $template = 'PorchaProcessingBundle:Report/TopSheet:index_print.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Report/TopSheet:list.html.twig';
        }

        return $this->render($template, $data);
    }

    protected function prepareGroupByMouza($data)
    {
        $output = array();
        foreach ($data as $row) {
            $output[$row->getUpozila()->getName()]['rows'][] = $row;
        }

        return $output;
    }
}
