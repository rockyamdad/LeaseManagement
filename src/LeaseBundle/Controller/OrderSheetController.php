<?php

namespace LeaseBundle\Controller;

use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Entity\LeaseDetails;
use LeaseBundle\Form\GadgetLeaseType;
use LeaseBundle\Form\LeaseType;
use PorchaProcessingBundle\Entity\Mouza;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderSheetController extends Controller
{

    /**
     * @JMS\Secure(roles="ROLE_ORDER_SHEET_LIST")
     */
    public function indexAction(Request $request,$type)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);
        
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findAssignedApplication($type);
        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage, array('sort' => 'applications.id', 'direction'=>'DESC'));

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:OrderSheet:ordersheet_application_list_partial.html.twig',array(
                'applications'=>$Applications,
            ));
        }
        else{
            return $this->render('LeaseBundle:OrderSheet:ordersheet_application_list.html.twig',array(
                'applications'=>$Applications,
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_ORDER_SHEET_LIST")
     */
    public function gadgetListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findAssignedGadgetApplication();

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage, array('sort' => 'applications.id', 'direction'=>'DESC'));

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:OrderSheet:ordersheet_application_list_partial.html.twig',array(
                'applications'=>$Applications,
            ));
        }
        else{
            return $this->render('LeaseBundle:OrderSheet:ordersheet_application_list.html.twig',array(
                'applications'=>$Applications,
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_LEASE_WISE_ORDER_SHEET_LIST")
     */
    public function LeaseWiseOrderSheetListAction(Request $request)
    {
        $leaseId=$request->attributes->get('id');
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findBy(array('status'=>array('ARCHIVED','APPROVED'),'lease'=>$leaseId),array('id' => 'DESC'));
        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);
        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:OrderSheet:ordersheet_lease_wise_application_list_partial.html.twig',array(
                'applications'=>$Applications,
            ));
        }
        else{
            return $this->render('LeaseBundle:OrderSheet:ordersheet_lease_wise_application_list.html.twig',array(
                'applications'=>$Applications,
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_ORDER_SHEET_VIEW")
     */
    public function viewAction(Application $application)
    {
        $applicationDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $application->getId(),'entity'=>'application') );
        $leaseTerminatedDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $application->getId(),'entity'=>'application_terminated') );
        if($application->getLease()->getGadget()){
            $gadgetDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$application->getLease()->getGadget()->getId(),'entity'=>'gadget') );
        }else $gadgetDocx= null;
        if($application->getLease()){
            $leaseDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$application->getLease()->getId(),'entity'=>'lease') );
        }else $leaseDocx= null;
        $registerSix= $this->getDoctrine()->getManager()->getRepository('LeaseBundle:RegisterLeaseSix')->findBy(array('applications'=> $application->getId()) );
        if($registerSix){
            foreach ($registerSix as $registerId){
                $registerDocx [] = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $registerId->getId(),'entity'=>'register') );
            }
        }else{
            $registerDocx='';
        }
        return $this->render('@Lease/OrderSheet/view.html.twig', array(
            'application' => $application,
            'applicationDocx' => $applicationDocx,
            'leaseRegisterSixDocs' => '',
            'leaseTerminatedDocx' => $leaseTerminatedDocx,
            'gadgetDocx' => $gadgetDocx,
            'leaseDocx' => $leaseDocx,
        ));
    }

}
