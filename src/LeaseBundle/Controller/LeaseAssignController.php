<?php

namespace LeaseBundle\Controller;

use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Entity\PaymentSchedule;
use LeaseBundle\Entity\RegisterLeaseSix;
use LeaseBundle\Form\LeaseStatusChangeType;
use LeaseBundle\Form\LeaseType;
use LeaseBundle\Form\RegisterLeaseSixType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaseAssignController extends BaseController
{
    /**
     * @JMS\Secure(roles="ROLE_ASSIGN_LEASE_CREATE")
     */
    public function createAction(Request $request, Application $application)
    {
        $leaseAssign = new RegisterLeaseSix();
        $chalan = $this->getDueChallanAmount($application);

        $submittedChallanAmount = $request->request->get('leasebundle_registerleasesix[chalanAmount]', null, true);
        $form = $this->createForm(new RegisterLeaseSixType($chalan, $submittedChallanAmount), $leaseAssign);
        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
            $leaseAssign->setApplications($application);
            $leaseAssign->setStatus('APPROVED');
            $leaseAssign->setCreatedDateTime(new \DateTime());

            if(isset( $request->request->all()['demandFee'])) {

                $totalAmount = $request->request->all()['demandFee'];
                $leaseAssign->getApplications()->setTotalAmount($totalAmount);
            }else{
                $totalAmount = $application->getTotalAmount();
            }
            $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($leaseAssign);
            $this->paymentScheduleCreate($request,$application, $leaseAssign, $totalAmount);
            $this->registerFileUpload($request, $leaseAssign);

            $this->addFlash('success', 'লিজ সফলভাবে বরাদ্দ করা হয়েছে');

            return $this->redirectToRoute('lease_assign_list');
        }
    }

        return $this->render('LeaseBundle:LeaseAssign:create.html.twig',array(
            'form' =>$form->createView(),
            'application' =>$application
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_ASSIGN_LEASE_LIST")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allAssignLeases = $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->getAllAssignLeases();
        $assignLeases = $this->get('knp_paginator')->paginate($allAssignLeases, $page, $perPage, array('sort' => 'r.id', 'direction'=>'DESC'));

        if($request->isXmlHttpRequest()){

            return $this->render('LeaseBundle:LeaseAssign:assign_lease_list_partial.html.twig',array(
                'assignLeases'=>$assignLeases
            ));
        }
        else{

            return $this->render('LeaseBundle:LeaseAssign:lease_assign_list.html.twig',array(
                'assignLeases'=>$assignLeases
            ));
        }

    }

    public function challanAmountAction($demandFee){
        $one1 = $demandFee/3;
        $one = round((float)$one1,2);
        $two2 = ($demandFee/3)*2;
        $two = round((float)$two2,2);
        $data[] = array($one,$two,$demandFee);

        $str = "";
        foreach ($data[0] as $key=>$value) {
            $str .=  "<option value = $value > $value</option> ";
        }
        return new Response($str);
    }
    public function challanAmountMarketAction($demandFee){

        $str = "";
        $str .=  "<option value = $demandFee 'selected'> $demandFee</option> ";
        return new Response($str);
    }

    /**
     * @JMS\Secure(roles="ROLE_ASSIGN_LEASE_EDIT")
     */
    public function updateAction(Request $request, RegisterLeaseSix $leaseAssign)
    {
        $chalan = $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->getChalanAmount($leaseAssign->getApplications());
        $form = $this->createForm(new RegisterLeaseSixType($chalan), $leaseAssign);
        $documents = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$leaseAssign->getId(),'entity'=>'register'));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $leaseAssign->setStatus('WAITING_FOR_APPROVAL');
                $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($leaseAssign);
                $this->registerFileUpload($request, $leaseAssign);

                $this->addFlash('success', 'লিজ বরাদ্দ সফলভাবে আপডেট করা হয়েছে');
                return $this->redirectToRoute('lease_assign_list');
            }
        }

        return $this->render('LeaseBundle:LeaseAssign:update.html.twig',array(
            'form' =>$form->createView(),
            'documents'=>$documents
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_ASSIGN_LEASE_VIEW")
     */
    public function viewAction(RegisterLeaseSix $leaseAssign)
    {
        $leaseRegisterSixDocs = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $leaseAssign->getId(),'entity'=>'register') );
        return $this->render('LeaseBundle:LeaseAssign:view.html.twig', array(
            'leaseAssign' => $leaseAssign,
            'leaseAssignDocs' => $leaseRegisterSixDocs,
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_LEASE_ASSIGN_APPROVED, ROLE_LEASE_ASSIGN_TERMINATED")
     */
    public function leaseAssignStatusChangeAction(Request $request, RegisterLeaseSix $leaseAssign, $status)
    {
        if($status == 'WAITING_FOR_APPROVAL'){
            $leaseAssign->setStatus('APPROVED');
        }elseif($status == 'APPROVED'){
            $leaseAssign->setStatus('TERMINATED');
        }else{
            $leaseAssign->setStatus('CORRECTION');
        }
        $this->addFlash('success', 'অবস্থা সফলভাবে পরিবর্তিত হয়েছে');
        $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($leaseAssign);
        return $this->redirectToRoute('lease_assign_list');
    }



    /**
     * @JMS\Secure(roles="ROLE_DOCUMENT_DELETE")
     */
    public function documentDeleteAction(Request $request,Document $document)
    {
        $document = $this->getDoctrine()->getRepository('LeaseBundle:Document')->find($document->getId());
        if (!$document) {
            throw $this->createNotFoundException('No guest found for id '.$document->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Document')->delete($document);
        return new JsonResponse($document);
    }
    
    /**
     * @JMS\Secure(roles="ROLE_WAITING_FOR_TERMINATE_LEASE_LIST")
     */
    public function waitingForTerminateLeaseListAction(Request $request,$type)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getWaitingforTerminateLeaseList($type);
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:LeaseAssign:waiting_for_terminate_lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:LeaseAssign:waiting_for_terminate_lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_WAITING_FOR_TERMINATE_LEASE_STATUS_CHANGE")
     */
    public function waitingForTerminateLeaseStatusChangeAction(Request $request,Lease $leaseId,$status)
    {
        $applicantion = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Application')->findOneBy(array('id'=> $leaseId->getApplication()->getId()) );
        $applicant = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Applicant')->findOneBy(array('application'=> $applicantion->getId()) );
        if($status == 'Approve'){
                $gadget = $applicantion->getLease()->getGadget();
                if($gadget){
                    $gadget->setLease(NULL);
                }

                if( $leaseId->getTender() !='1'){
                    $applicantion->getLease()->setStatus('TERMINATED');
                }else{
                    $applicantion->getLease()->setStatus('ACTIVE');
                }
                $applicantion->getLease()->setApplication(null);
                $applicantion->setStatus('ARCHIVED');
            if($applicantion->getLease()->getType() !='Gadget'){
                    foreach ($applicantion->getRegisterSix() as $register){
                        $register->setStatus('ARCHIVED');
                    }
                }
            $phoneNumber=$applicant->getPhoneNo();
            $smsText = 'আপনার লিজ সেবা টি বন্ধ করে দেওয়া হয়েছে, ধন্যবাদ';
            $this->get('sms.transporter')->send($phoneNumber, $smsText);
            $this->addFlash('success', 'লিজ সেবা টি সফলভাবে বন্ধ করে দেওয়া হয়েছে');

            $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($applicantion);
            if($leaseId->getType() =='Market') {
                return $this->redirectToRoute('market_open_lease_list');
            }elseif($leaseId->getType() =='WaterBody') {
                return $this->redirectToRoute('open_lease_list');
            }else {
                return $this->redirectToRoute('gadget_open_list');
            }
        }
        else{
            $applicantion->getLease()->setStatus('CLOSED');
            $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($applicantion);
            if($leaseId->getType() =='Market') {
                return $this->redirectToRoute('approved_lease_list', array('type'=>'Market'));
            }elseif($leaseId->getType() =='WaterBody') {
                return $this->redirectToRoute('approved_lease_list', array('type'=>'WaterBody'));
            }else {
                return $this->redirectToRoute('gadget_list');
            }

        }


    }


    /**
     * @JMS\Secure(roles="ROLE_LEASE_STATUS_CHANGE")
     */
    public function leaseStatusChangeAction(Request $request, Application $applicationId)
    {

        $form = $this->createForm(new LeaseStatusChangeType());
        $terminatedRegisterSixDocs = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $applicationId->getId(),'entity'=>'application_terminated') );
        $applicant = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Applicant')->findOneBy(array('application'=> $applicationId->getId()) );
        $lease= $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Lease')->findOneBy(array('application'=> $applicationId->getId()) );
        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $type= $lease->getType();
                $currentStatus=$applicationId->getLease()->getStatus();
                $status = $request->request->all()['lease_status_change']['status'];
                if($currentStatus != 'TERMINATED' ){
                    if($status== 'TERMINATED'){
                        $lease->setStatus('WAITING_FOR_TERMINATE');
                    }
                    else{
                        $lease->setStatus($status);
                    }
                }
                else{
                    $lease->setStatus($status);
                }
                $this->addFlash('success', 'অবস্থা সফলভাবে পরিবর্তিত হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->create($applicationId);

                if(isset( $request->files->all()['doc_file'])) {
                    /** @var UploadedFile $file */
                    $files = $request->files->all()['doc_file'];
                    foreach($files as $file){
                        $document = new Document();
                        $uploaded = $file->move($document->getUploadRootDir(), $file->getClientOriginalName());
                        if ($uploaded) {
                            $document->setRefId($applicationId->getId());
                            $document->setEntity('application_terminated');
                            $document->setType($request->request->all()['caption']);
                            $document->setPath($uploaded->getFilename());
                        }
                        $this->getDoctrine()->getRepository('LeaseBundle:Document')->create($document);
                    }
                }
                if($_POST['comment'] != '' ){

                    $this->commentCreate($applicationId);
                }

                if($type == 'Market'){
                    return $this->redirectToRoute('waiting_for_terminate_lease_list', array('type'=>'Market'));
                }else if($type == 'WaterBody') {
                    return $this->redirectToRoute('waiting_for_terminate_lease_list', array('type'=>'WaterBody'));
                }else{
                    return $this->redirectToRoute('waiting_for_terminate_lease_list', array('type'=>'Gadget'));
                }


            }
        }

        return $this->render('LeaseBundle:LeaseAssign:LeaseStatusChange.html.twig',array(
            'form' =>$form->createView(),
            'applicant' => $applicant,
            'terminatedAssignDocs' => $terminatedRegisterSixDocs
        ));
    }

    /**
     * @param Application $application
     * @return mixed
     */
    protected function getDueChallanAmount(Application $application)
    {
        if ($application->getTotalAmount() != null) {

            $totalChalans = $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->findBy(array('applications' => $application));
            $amount = 0;
            foreach ($totalChalans as $totalChalan) {
                $amount = $amount + $totalChalan->getChalanAmount();
            }
            $oneKisti = $application->getTotalAmount() / 3;
            $kisti = round($amount, 2) / round($oneKisti, 2);
            if ($kisti == 1) {
                $chalan = $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->getChalanAmount($application);

            } else {
                $chalan = $this->getDoctrine()->getRepository('LeaseBundle:RegisterLeaseSix')->getChallanAmountForOneyear($application);

            }

            return $chalan;
        }else{
            return '';
        }

    }


}
