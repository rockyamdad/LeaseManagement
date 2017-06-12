<?php

namespace LeaseBundle\Controller;

use LeaseBundle\Entity\Comment;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Form\ApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class ApplicationController extends BaseController
{
    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_LIST")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);


        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findWaterBodyApplication();

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Application:application_list_partial.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
        else{
            return $this->render('LeaseBundle:Application:application_list.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_LIST")
     */
    public function marketLeaseApplicationAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);


        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findMarketApplication();

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Application:application_list_partial.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
        else{
            return $this->render('LeaseBundle:Application:application_list.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_APPROVED_APPLICATION_LIST")
     */
    public function ApprovedLeaseApplicationListAction(Request $request)
    {
        $type=$request->attributes->get('type');
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findApprovedApplications($type);

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);


        if($request->isXmlHttpRequest()){
            return $this->render('@Lease/Application/lease_wise_application_list_partial.html.twig',array(
                'applications'=>$Applications,
            ));
        }
        else{
            return $this->render('@Lease/Application/lease_wise_application_list.html.twig',array(
                'applications'=>$Applications,
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_LIST")
     */
    public function gadgetListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findGadgetApplication();

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Application:application_list_partial.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
        else{
            return $this->render('LeaseBundle:Application:application_list.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_MANUAL_APPLICATION_CREATE")
     */
    public function manualCreateAction(Request $request, Lease $lease)
    {
        $routee = $this->getRequest()->headers->get('referer');
        $application = new Application();
        $form = $this->createForm(new ApplicationType(), $application);
        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $application->setLease($lease);
                $application->setCreatedDateTime(new \DateTime());
                $application->setStatus('WAITING_FOR_APPROVAL');
                $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findby(array('lease'=>$lease));

                if($allApplications){
                    foreach($allApplications as $app){
                        $app->setStatus('REJECTED');
                        $this->getDoctrine()->getRepository('LeaseBundle:Application')->create($app);
                    }
                }

                $this->getDoctrine()->getRepository('LeaseBundle:Application')->create($application);

                if(isset( $request->files->all()['doc_file'])) {
                    /** @var UploadedFile $file */
                    $files = $request->files->all()['doc_file'];
                    foreach($files as $file){
                        $document = new Document();
                        $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                        if ($uploaded) {
                            $document->setRefId($application->getId());
                            $document->setEntity('application');
                            $document->setPath($uploaded->getFilename());
                        }
                        $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
                    }
                }
                $this->addFlash('success', 'আপনার আবেদন সফল হয়েছে.');
                if( $routee == 'http://keranigonj_elrs.local/application/lease/application/list'){

                    return $this->redirectToRoute('lease_application_list');
                }else{
                    return $this->redirectToRoute('lease_gadget_application_list');
                }
            }
        }

        return $this->render('LeaseBundle:Application:manualCreate.html.twig',array(
            'form' =>$form->createView()
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_LEASE_WISE_APPLICATION_LIST")
     */
    public function LeaseWiseApplicationListAction(Request $request)
    {
        $leaseId=$request->attributes->get('id');
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);
        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        if($userType['name'] == 'ইউ এন ও অ্যাডমিন'){
            $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findBy(array('status'=>array('WAITING_FOR_APPROVAL','APPROVED'),'lease'=>$leaseId),array('id' => 'DESC'));
        }
        else{
            $allApplications = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findBy(array('status'=>array('WAITING_FOR_APPROVAL','APPROVED','PENDING','CORRECTION'),'lease'=>$leaseId),array('id' => 'DESC'));
        }

        $Applications = $this->get('knp_paginator')->paginate($allApplications, $page, $perPage);


        if($request->isXmlHttpRequest()){
            return $this->render('@Lease/Application/lease_wise_application_list_partial.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
        else{
            return $this->render('@Lease/Application/lease_wise_application_list.html.twig',array(
                'applications'=>$Applications,
                'userType'=> $userType['name']
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_VIEW")
     */
    public function viewAction(Application $application)
    {
        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $applicationDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $application->getId(),'entity'=>'application') );
        $comment = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Comment')->findOneBy(array('refId'=>'APP_'.$application->getId()),array('id'=>'DESC') );

        return $this->render('@Lease/Application/view.html.twig', array(
            'application' => $application,
            'applicationDocx' => $applicationDocx,
            'comment' => $comment,
            'userType'=> $userType['name']
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_VIEW")
     */
    public function printViewAction(Application $application)
    {

        return $this->render('@Lease/Application/printView.html.twig', array(
            'application' => $application,
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_APPLICATION_STATUS_CHANGE")
     */
    public function ApplicationStatusChangeAction(Request $request) {

        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $applicationId = $request->attributes->get('applicationId');
        //$status = $request->attributes->get('status');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('LeaseBundle:Application');
        $application = $repository->findOneBy(array(
            'id'=> $applicationId,
        ));

        $leaseId=$application->getLease()->getId();
        if($userType['name'] =='এসি-ল্যান্ড'){
            $application->setStatus('WAITING_FOR_APPROVAL');
            $this->addFlash('success', 'আবেদনটি সফলভাবে অনুমোদনের জন্য পাঠানো হয়েছে ');
            $this->doctrineManager()->persist($application);
            $this->doctrineManager()->flush();
        }
        else{
            if(isset($_POST['approval'])) {
                $leaseWiseApplication = $repository->findApplicationOfPortal($leaseId);
                foreach ($leaseWiseApplication as $appl){
                    $appl->setStatus('REJECTED');
                }
                $application=$repository->findOneBy(array(
                    'lease'=> $leaseId,
                    'id'=>$application->getId()
                ));
                $application->setStatus('APPROVED');
                $application->setApprovedDateTime(new \DateTime());
                $application->getLease()->setStatus('CLOSED');
                $application->getLease()->setTender(null);
                if($application->getLease()->getGadget()){
                    $application->getLease()->getGadget()->setStatus('APPROVED');
                }
                $application->getLease()->setApplication($application);
                $smsText = 'Apnar abedon ti nirbachon kora hoyeche apnar tracking number  '.$application->getApplicationTrackingId().'.';
                $this->get('sms.transporter')->send($application->getPhoneNo(), $smsText);

                $this->addFlash('success', 'আবেদনটি অনুমোদিত হয়েছে');
            }else{
                $application->setStatus('CORRECTION');
                $this->addFlash('success', 'আবেদনটি সংশোধনের জন্য পাঠানো হয়েছে');
            }

            $this->doctrineManager()->persist($application);
            $this->doctrineManager()->flush();
        }

        if($_POST['comment'] != '' ){

            $this->commentCreate($application);
        }
        
        return $this->redirectToRoute('lease_application_view',array('id'=> $applicationId) );
    }
    public function doctrineManager() {

        return $this->getDoctrine()->getManager();
    }
    /**
     * @JMS\Secure(roles="ROLE_DOCUMENT_DELETE")
     */
    public function documentDeleteAction(Request $request,Document $document) {

        $document = $this->getDoctrine()->getRepository('LeaseBundle:Document')->find($document->getId());
        if (!$document) {
            throw $this->createNotFoundException('No guest found for id '.$document->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Document')->delete($document);

        return new JsonResponse($document);
    }

  

}
