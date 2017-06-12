<?php

namespace LeaseBundle\Controller;

use LeaseBundle\Entity\Applicant;
use LeaseBundle\Entity\Comment;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Gadget;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Form\GadgetLeaseType;
use LeaseBundle\Form\GadgetRenewType;
use LeaseBundle\Form\GadgetType;
use LeaseBundle\Entity\GadgetDetails;
use LeaseBundle\Form\LeaseAddToPortalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GadgetController extends BaseController
{
    /**
     * @JMS\Secure(roles="ROLE_GADGET_LIST")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allGadgets = $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->allGadget();
        $gadgets = $this->get('knp_paginator')->paginate($allGadgets, $page, $perPage);
        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Gadget:gadget_list_partial.html.twig',array(
                'gadgets'=>$gadgets
            ));
        }
        else{
            return $this->render('LeaseBundle:Gadget:list.html.twig',array(
                'gadgets'=>$gadgets
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_OPEN_GADGET_LIST")
     */
    public function allOpenGadgetAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);
        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        if($userType['name'] =='এসি-ল্যান্ড'){
            $allGadgets = $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->forACLandAllOpenGadget();
        }else {
            $allGadgets = $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->forUnoAllOpenGadget();
        }
        $gadgets = $this->get('knp_paginator')->paginate($allGadgets, $page, $perPage);
        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Gadget:open_gadget_list_partial.html.twig',array(
                'gadgets'=>$gadgets
            ));
        }
        else{
            return $this->render('LeaseBundle:Gadget:openGadget.html.twig',array(
                'gadgets'=>$gadgets
            ));
        }


    }

    /**
     * @JMS\Secure(roles="ROLE_GADGET_CREATE")
     */
    public function createAction(Request $request)
    {
        $gadget = new Gadget();
        $form = $this->createForm(new GadgetType(), $gadget);

        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));
        if($request->getMethod()=="POST")
        {
            
            $form->handleRequest($request);
            if($form->isValid()) {
                $gadget->setStatus('PENDING');
                $this->setLeaseForGadget($form, $gadget);
                $gadget->leases[0]->getApplications()[0]->setStatus('PENDING');
                $gadget->leases[0]->setCreatedDateTime(new \DateTime());
                $applicationTrackingId = $this->createTrackingNo();
                $gadget->leases[0]->getApplications()[0]->setCreatedDateTime(new \DateTime());
                $gadget->leases[0]->getApplications()[0]->setApplicationTrackingId($applicationTrackingId);
                $gadget->leases[0]->getApplications()[0]->getApplicant()->setApplication($gadget->leases[0]->getApplications()[0]);
                $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->create($gadget);
                $this->allDocumentUpload($request, $gadget->getLeases()[0]->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'gadget',$gadget);
                $this->publicDocumentUpload($request, 'gadget',$gadget);
                
                $this->addFlash('success', 'গ্যাজেট সফলভাবে তৈরি করা হয়েছে');

                return $this->redirectToRoute('gadget_open_list');
            }
            

        }

        return $this->render('@Lease/Gadget/create.html.twig',array(
            'form' =>$form->createView(),
            'mouzas' =>$mouzas,
        ));
    }
    
    /**
     * @JMS\Secure(roles="ROLE_GADGET_VIEW")
     */
    public function viewAction(Gadget $gadget)
    {
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Private'));
        $lease = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Lease')->findOneBy(array('gadget'=> $gadget) );
        $application = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Application')->findApplicationForGadget($lease);
        $applicant = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Applicant')->findOneBy(array('application'=>$application));
        $applicationId=$gadget->getLeases()[0]->getApplications()[0]->getId();
        $comment = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Comment')->findOneBy(array('refId'=>'GADGET_'.$gadget->getId()),array('id'=>'DESC') );
        $userType = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $applicationDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $applicationId,'entity'=>'application') );
        return $this->render('@Lease/Gadget/view.html.twig', array(
            'gadget' => $gadget,
            'userType' => $userType['name'],
            'applicant' => $applicant,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'comment' => $comment,
            'applicationDocx' => $applicationDocx,
        ));
    }


    /**
     * @JMS\Secure(roles="ROLE_GADGET_RENEW")
     */
    public function renewAction(Request $request,Gadget $gadget)
    {
        $leasee = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Gadget')->getLeasee($gadget);
        $lease = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Lease')->findOneBy(array('gadget'=>$gadget,'status'=>'ACTIVE'));
        $form = $this->createForm(new GadgetLeaseType(), $lease);

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createLease($lease);

                if(isset( $request->files->all()['doc_file'])) {
                    /** @var UploadedFile $file */
                    $files = $request->files->all()['doc_file'];
                    foreach($files as $file){
                        $document = new Document();
                        $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                        if ($uploaded) {
                            $document->setRefId($lease->getId());
                            $document->setEntity('lease');
                            $document->setPath($uploaded->getFilename());
                        }
                        $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
                    }
                }
                $this->addFlash('success', 'গ্যাজেট সফলভাবে নবায়ন হয়েছে');
                return $this->redirectToRoute('gadget_list');
            }
        }

        return $this->render('LeaseBundle:Gadget:renew.html.twig',array(
            'leasees' =>$leasee,
            'form' =>$form->createView()
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_GADGET_ADD_TO_PORTAL")
     */
    public function addToPortalAction(Request $request,Gadget $gadget)
    {
        $lease = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Lease')->findOneBy(array('gadget'=>$gadget->getId(),'status'=>'TERMINATED'));
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Private'));

        $form = $this->createForm(new LeaseAddToPortalType(), $lease);

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {

                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+1;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));

                $lease->setStatus('ACTIVE');
                $lease->setGadget($gadget);
                $gadget->setLease($lease);
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createLease($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'Gadget',  $lease);

                $this->publicDocumentUpload($request, 'Gadget', $lease);

                $this->addFlash('success', 'গ্যাজেট সফলভাবে পোর্টাল যোগ করা হয়েছে');
                return $this->redirectToRoute('gadget_open_list');
            }
        }

        return $this->render('@Lease/Gadget/addToPortal.html.twig',array(
            'gadget' =>$gadget,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'form' =>$form->createView()
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_GADGET_APPROVED, ROLE_GADGET_TERMINATED")
     */
    public function statusChangeAction(Request $request, Gadget $gadget, $status)
    {
        $lease = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Lease')->findOneBy(array('gadget'=>$gadget,'status'=>'ACTIVE'));

        if(isset($_POST['approval'])) {
            $gadget->setStatus('APPROVED');
            $gadget->setLease($lease);
            $application = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Application')->findOneBy(array('lease'=>$lease,'status'=>'PENDING'));
            $application->setStatus('APPROVED');
            $lease->setStatus('CLOSED');
            $lease->setApplication($application);
            $this->addFlash('success', 'আবেদনটি সফল ভাবে অনুমোদন করা হয়েছে ');
        }else  if(isset($_POST['correction']))  {
            $gadget->setStatus('CORRECTION');
            $this->addFlash('success', 'আবেদনটি সংশোধনের জন্য পাঠানো হয়েছে ');
        }else{
            $gadget->setStatus('WAITING_FOR_APPROVAL');
            $this->addFlash('success', 'আবেদনটি সফলভাবে অনুমোদনের জন্য পাঠানো হয়েছে ');
        }

        if($_POST['comment'] != '' ){

            $this->commentCreate($gadget);
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->create($gadget);
        if( $gadget->getStatus() == 'APPROVED'){
            return $this->redirectToRoute('gadget_list');
        }else {
            return $this->redirectToRoute('gadget_open_list');
        }

    }
    /**
     * @param $gadget
     */
    protected function commentCreate($gadget) {

        $type = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $comment = new Comment();
        $comment->setCreatedDate(new \DateTime());
        $comment->setMessage($_POST['comment']);
        $comment->setUsersBy($this->getUser());
        $comment->setUserRole($type['name']);
        $comment->setRefId('GADGET_'.$gadget->getId());
        $this->getDoctrine()->getRepository('LeaseBundle:Comment')->create($comment);

    }

    /**
     * @JMS\Secure(roles="ROLE_GADGET_EDIT")
     */
    public function updateAction(Request $request,Gadget $gadget)
    {
        $lease= $this->getDoctrine()->getRepository('LeaseBundle:Lease')->findOneBy(array('gadget'=>$gadget->getId()));
        $documents = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getApplication()->getId(),'entity'=>'application'));
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$gadget->getId(),'entity'=>'gadget','privacy' => 'Private'));

        $form = $this->createForm(new GadgetType(), $gadget);

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);
            if($form->isValid()){
                $gadget->setStatus('PENDING');
                $leases = $form->get('leases')->getData();
                $fiscaleYear = $leases->get(0)->getFiscalYear();
                $gadget->leases[0]->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear = $fiscaleYear + 1;
                $gadget->leases[0]->setEndDate(date_create("$fiscaleYear-04-13"));
                $this->getDoctrine()->getRepository('LeaseBundle:Gadget')->create($gadget);

                $this->allDocumentUpload($request, $gadget->getLeases()[0]->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'gadget',$gadget);
                $this->publicDocumentUpload($request, 'gadget',$gadget);
                $this->addFlash('success', 'গ্যাজেট সফলভাবে আপডেট করা হয়েছে');
                return $this->redirectToRoute('gadget_open_list');
            }
        }
        return $this->render('@Lease/Gadget/update.html.twig',array(
            'form'=>$form->createView(),
            'documents' =>$documents,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
        ));
    }
    
    
    public function gadgetDetailsDeleteAction(Request $request,GadgetDetails $details)
    {
        $gadgetDetails = $this->getDoctrine()->getRepository('LeaseBundle:GadgetDetails')->find($details->getId());
        if (!$gadgetDetails) {
            throw $this->createNotFoundException('No guest found for id '.$gadgetDetails->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:GadgetDetails')->delete($gadgetDetails);
        return new JsonResponse($gadgetDetails);
    }
    
    
    public function applicantDetailsDeleteAction(Request $request,Applicant $details)
    {

        $applicantDetails = $this->getDoctrine()->getRepository('LeaseBundle:Applicant')->find($details->getId());
        if (!$applicantDetails) {
            throw $this->createNotFoundException('No guest found for id '.$applicantDetails->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Applicant')->delete($applicantDetails);
        return new JsonResponse($applicantDetails);
    }

    /**
     * @param $form
     * @param $gadget
     */
    protected function setLeaseForGadget($form, $gadget)
    {
        $leases = $form->get('leases')->getData();
        $fiscaleYear = $leases->get(0)->getFiscalYear();
        $gadget->leases[0]->setStartDate(date_create("$fiscaleYear-04-14"));
        $fiscaleYear = $fiscaleYear + 1;
        $gadget->leases[0]->setEndDate(date_create("$fiscaleYear-04-13"));
        $gadget->leases[0]->setStatus('ACTIVE');
        $gadget->leases[0]->setType('Gadget');
        $gadget->leases[0]->setApplication($gadget->leases[0]->getApplications()[0]);
    }


}
