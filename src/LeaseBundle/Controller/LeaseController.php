<?php

namespace LeaseBundle\Controller;

use DateTimeZone;
use EasyBanglaDate\Types\BnDateTime;
use LeaseBundle\Entity\Comment;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\History;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Entity\WaterBodyDetails;
use LeaseBundle\Form\GadgetLeaseType;
use LeaseBundle\Form\LeaseAddToPortalType;
use LeaseBundle\Form\LeaseType;
use PorchaProcessingBundle\Entity\Mouza;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class LeaseController extends BaseController
{
    /**
     * @JMS\Secure(roles="ROLE_APPROVED_LEASE_LIST")
     */
    public function approvedLeaseListAction(Request $request,$type)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getApprovedLeaseList($type);
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage, array('sort' => 'l.id', 'direction'=>'DESC'));
        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Lease:lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Lease:lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_WAITING_FOR_RENEW_APPROVAL_LEASE_LIST")
     */
    public function waitingForApprovalLeaseListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getWaitingforApprovalLeaseList();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage, array('sort' => 'l.id', 'direction'=>'DESC'));

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Lease:waiting_for_renew_approval_lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Lease:waiting_for_renew_approval_lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_OPEN_WATER_BODY_LEASE_LIST")
     */
    public function openLeaseListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getOpenWaterBodyLeaseList();
        
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Lease:open_lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Lease:open_lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_OPEN_MARKET_BODY_LEASE_LIST")
     */
    public function marketLeaseListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getOpenMarketLeaseList();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Lease:open_lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Lease:open_lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_WAITING_LEASE_LIST")
     */
    public function waitingLeaseListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->allWaitingLeases();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage, array('sort' => 'l.id', 'direction'=>'DESC'));
        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Lease:waiting_lease_list_partial.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Lease:waiting_lease_list.html.twig',array(
                'leases'=>$leases
            ));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_WATER_BODY_LEASE_CREATE")
     */
    public function createAction(Request $request)
    {

        $lease = new Lease();
        $form = $this->createForm(new LeaseType(null,null,1), $lease);
        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {

                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+3;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                $lease->setCreatedDateTime(new \DateTime());
                $lease->setType('WaterBody');
                $lease->setStatus('ACTIVE');
                $this->addFlash('success', 'জল মহল লিজ সফলভাবে তৈরি করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->create($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease',$lease);


                return $this->redirectToRoute('open_lease_list');
            }
        }
        return $this->render('@Lease/Lease/waterBodyLeaseCreate.html.twig',array(
            'form' =>$form->createView(),
            'surveys' =>$surveys,
            'mouzas' =>$mouzas,
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_MARKET_BODY_LEASE_CREATE")
     */
    public function marketBodyCreateAction(Request $request)
    {

        $lease = new Lease();
        $form = $this->createForm(new LeaseType(null,null,1), $lease);
        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {

                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+1;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                $lease->setType('Market');
                $lease->setCreatedDateTime(new \DateTime());
                $lease->getMarketDetail()->setLease($lease);
                $lease->setStatus('ACTIVE');
                $this->addFlash('success', 'লিজ সফলভাবে তৈরি করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createMarket($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease',  $lease);

                $this->publicDocumentUpload($request, 'lease', $lease);

                return $this->redirectToRoute('market_open_lease_list');
            }
        }

        return $this->render('@Lease/Lease/marketBodyLeaseCreate.html.twig',array(
            'form' =>$form->createView(),
            'surveys' =>$surveys,
            'mouzas' =>$mouzas,
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_LEASE_ADD_TO_PORTAL")
     */
    public function addToPortalAction(Request $request,Lease $lease)
    {

        $form = $this->createForm(new LeaseAddToPortalType(), $lease);
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        if($request->getMethod()=="POST")
        {

            $form->handleRequest($request);
            if($form->isValid()) {
                if($lease->getType() == 'WaterBody'){
                    $fiscaleYear = $form->get('fiscalyear')->getData();
                    $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                    $fiscaleYear=$fiscaleYear+3;
                    $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                    if($lease->getTender() or $lease->getStatus() == 'TERMINATED'){
                        $lease->setStatus('ACTIVE');
                        $lease->setTender(null);
                    }else{
                        $lease->setTender('1');
                    }
                    


                }
                else{
                    $fiscaleYear = $form->get('fiscalyear')->getData();
                    $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                    $fiscaleYear=$fiscaleYear+1;
                    $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                    $lease->setStatus('ACTIVE');
                }

                $this->addFlash('success', 'লিজ সফলভাবে পোর্টাল যোগ করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createLease($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease',  $lease);

                $this->publicDocumentUpload($request, 'lease', $lease);

                if($lease->getType() == 'Market'){
                    return $this->redirectToRoute('market_open_lease_list');
                }else{
                    return $this->redirectToRoute('open_lease_list');
                }

            }
        }

        return $this->render('@Lease/Lease/addToPortal.html.twig',array(
            'lease' =>$lease,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'form' =>$form->createView()
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_LEASE_VIEW")
     */
    public function viewAction(Lease $lease)
    {
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        $application = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Application')->findOneBy(array('lease'=> $lease,'status'=>'APPROVED') );
        $leaseComment = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Comment')->findOneBy(array('refId'=>'LEASE_'.$lease->getId()),array('id'=>'DESC') );
        if($application){
            $applicationComment = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Comment')->findOneBy(array('refId'=>'APP_'.$application->getId()),array('id'=>'DESC') );
            $registerSix= $this->getDoctrine()->getManager()->getRepository('LeaseBundle:RegisterLeaseSix')->findBy(array('applications'=> $application->getId()) );
            if($registerSix){
                foreach ($registerSix as $registerId){
                    $leaseRegisterSixDocs [] = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $registerId->getId(),'entity'=>'register') );
                }
            }else{
                $leaseRegisterSixDocs='';
            }
            $applicationDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $application->getId(),'entity'=>'application') );
            foreach ($application->getRegisterSix() as $data){
                $registerSixData[]= $data;
            }

            return $this->render('@Lease/Lease/view.html.twig', array(
                'lease' => $lease,
                'applicant' => $application->getApplicant(),
                'registerSix' => $registerSix,
                'LeasePublicDocuments' =>$PublicDocuments,
                'LeasePrivateDocuments' =>$PrivateDocuments,
                'applicationDocx' => $applicationDocx,
                'leaseComment' => $leaseComment,
                'applicationComment' => $applicationComment,
                'leaseRegisterSixDocs' => $leaseRegisterSixDocs,
            ));
        }
        return $this->render('@Lease/Lease/view.html.twig', array(
            'lease' => $lease,
            'applicant' => '',
            'registerSix' => '',
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'applicationDocx' => '',
            'leaseComment' => $leaseComment,
            'applicationComment' => '',
            'leaseRegisterSixDocs' => '',
        ));
    }
    
    /**
     * @JMS\Secure(roles="ROLE_LEASE_EDIT")
     */
    public function updateAction(Request $request,Lease $lease)
    {
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        $form = $this->createForm(new LeaseType(null,null,1),$lease);

        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);
            if($form->isValid()){
                $fiscaleYear=$request->request->all()['leasebundle_lease']['fiscalyear'];
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+3;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                $this->addFlash('success', 'জল মহল লিজ সফলভাবে আপডেট করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->create($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                if ($lease->getType() == 'Market')
                {
                    return $this->redirectToRoute('market_open_lease_list');
                }
                else
                    return $this->redirectToRoute('open_lease_list');
            }
        }

        return $this->render('@Lease/Lease/update.html.twig',array(
            'form'=>$form->createView(),
            'lease' =>$lease,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'surveys' =>$surveys,
            'mouzas' =>$mouzas
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_MARKET_LEASE_EDIT")
     */
    public function updateMarketLeaseAction(Request $request,Lease $lease)
    {
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        $form = $this->createForm(new LeaseType(null,null,1),$lease);

        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=='POST'){
            $form->handleRequest($request);
            if($form->isValid()){
                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+1;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));

                $lease->setStatus('ACTIVE');
                $this->addFlash('success', ' লিজ সফলভাবে আপডেট করা হয়েছে');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createMarket($lease);

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                return $this->redirectToRoute('market_open_lease_list');
            }
        }
        return $this->render('@Lease/Lease/updateMarketLease.html.twig',array(
            'form'=>$form->createView(),
            'lease' =>$lease,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'surveys' =>$surveys,
            'mouzas' =>$mouzas
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_DOCUMENT_DELETE")
     */
    public function documentDeleteAction(Request $request,Document $document)
    {
        $document = $this->getDoctrine()->getRepository('LeaseBundle:Document')->find($document->getId());
        if (!$document) {
            throw $this->createNotFoundException('No Document found for id '.$document->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:Document')->delete($document);
        return new JsonResponse($document);
    }

    /**
     * @JMS\Secure(roles="ROLE_LEASE_DETAILS_DELETE")
     */
    public function detailsDeleteAction(Request $request,WaterBodyDetails $details)
    {
        $leaseDetails = $this->getDoctrine()->getRepository('LeaseBundle:WaterBodyDetails')->find($details->getId());
        if (!$leaseDetails) {
            throw $this->createNotFoundException('No guest found for id '.$leaseDetails->getId());
        }
        $this->getDoctrine()->getRepository('LeaseBundle:WaterBodyDetails')->delete($leaseDetails);
        return new JsonResponse($leaseDetails);
    }
    /**
     * @JMS\Secure(roles="ROLE_PREVIOUS_LEASE_CREATE")
     */
    public function createPreviousMarketDataAction(Request $request)
    {
        $lease = new Lease();
        $chalan = '';
        $submittedChallanAmount = $request->request->get('leasebundle_lease[applications][0][registerSix][0][chalanAmount]', null, true);
        $form = $this->createForm(new LeaseType($chalan,$submittedChallanAmount), $lease);

        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+1;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                $lease->setType('Market');
                $lease->setCreatedDateTime(new \DateTime());
                $lease->getMarketDetail()->setLease($lease);
                $lease->setStatus('ACTIVE');
                if(isset( $request->request->all()['demandFee'])) {

                    $totalAmount = $request->request->all()['demandFee'];
                    $lease->getApplications()[0]->setTotalAmount($totalAmount);
                }
                $lease->getApplications()[0]->setStatus('APPROVED');
                $applicationTrackingId = $this->createTrackingNo();
                $lease->getApplications()[0]->setApplicationTrackingId($applicationTrackingId);
                $lease->getApplications()[0]->setCreatedDateTime(new \DateTime());
                $lease->getApplications()[0]->getRegisterSix()[0]->setStatus('APPROVED');
                $lease->getApplications()[0]->getRegisterSix()[0]->setApplications($lease->getApplications()[0]);
                $lease->getApplications()[0]->getApplicant()->setApplication($lease->getApplications()[0]);

                $lease->setApplication($lease->getApplications()[0]);

                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createPreviousMarket($lease);

                $this->allDocumentUpload($request, $lease->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                $this->registerFileUpload($request, $lease->getApplications()[0]->getRegisterSix()[0]);

                $this->addFlash('success', 'লিজ সফলভাবে তৈরি করা হয়েছে');
                return $this->redirectToRoute('waiting_lease_list');
            }
        }

        return $this->render('@Lease/Lease/createPreviousMarketLease.html.twig',array(
            'form' =>$form->createView(),
            'surveys' =>$surveys,
            'mouzas' =>$mouzas
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PREVIOUS_LEASE_EDIT")
     */
    public function PreviousMarketDataUpdateAction(Request $request,Lease $lease)
    {
        $chalan = '';

        $chalanAmount=$lease->getApplications()[0]->getRegisterSix()[0]->getChalanAmount();
        $form = $this->createForm(new LeaseType($chalan,$chalanAmount), $lease);
        $documents = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getApplication()->getId(),'entity'=>'application'));
        $regDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getApplications()[0]->getRegisterSix()[0]->getId(),'entity'=>'register'));
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));
        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {

                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+1;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));

                $lease->setStatus('ACTIVE');
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createPreviousMarket($lease);

                $this->allDocumentUpload($request, $lease->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                $this->registerFileUpload($request, $lease->getApplications()[0]->getRegisterSix()[0]);

                $this->addFlash('success', 'লিজ সফলভাবে আপডেট করা হয়েছে');
                return $this->redirectToRoute('waiting_lease_list');
            }
        }

        return $this->render('@Lease/Lease/updatePreviousMarketLease.html.twig',array(
            'form' =>$form->createView(),
            'lease' =>$lease,
            'surveys' =>$surveys,
            'documents' =>$documents,
            'regDocuments' =>$regDocuments,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'mouzas' =>$mouzas
        ));
    }
    /**
     * @JMS\Secure(roles="ROLE_PREVIOUS_WATER_LEASE_CREATE")
     */
    public function createPreviousWaterBodyAction(Request $request)
    {
        $lease = new Lease();
        $chalan = '';
        $submittedChallanAmount = $request->request->get('leasebundle_lease[applications][0][registerSix][0][chalanAmount]', null, true);
        $form = $this->createForm(new LeaseType($chalan,$submittedChallanAmount), $lease);

        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+3;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));
                $lease->setType('WaterBody');
                $lease->setCreatedDateTime(new \DateTime());

                $lease->setStatus('ACTIVE');
                if(isset( $request->request->all()['demandFee'])) {

                    $totalAmount = $request->request->all()['demandFee'];
                    $lease->getApplications()[0]->setTotalAmount($totalAmount);
                }

                $applicationTrackingId = $this->createTrackingNo();
                $lease->getApplications()[0]->setApplicationTrackingId($applicationTrackingId);
                $lease->getApplications()[0]->setCreatedDateTime(new \DateTime());
                $lease->getApplications()[0]->setStatus('APPROVED');
                $lease->getApplications()[0]->getRegisterSix()[0]->setStatus('APPROVED');
                $lease->getApplications()[0]->getRegisterSix()[0]->setApplications($lease->getApplications()[0]);
                $lease->getApplications()[0]->getApplicant()->setApplication($lease->getApplications()[0]);

                $lease->setApplication($lease->getApplications()[0]);

                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createPrevious($lease);
                $this->paymentScheduleCreate($request,$lease->getApplications()[0], $lease->getApplications()[0]->getRegisterSix()[0], $totalAmount);
                $this->allDocumentUpload($request, $lease->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                $this->registerFileUpload($request, $lease->getApplications()[0]->getRegisterSix()[0]);
                $this->addFlash('success', 'লিজ সফলভাবে তৈরি করা হয়েছে');
                return $this->redirectToRoute('waiting_lease_list');
            }
        }

        return $this->render('@Lease/Lease/createPreviousWaterLease.html.twig',array(
            'form' =>$form->createView(),
            'surveys' =>$surveys,
            'mouzas' =>$mouzas
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PREVIOUS_LEASE_EDIT")
     */
    public function updatePreviousWaterBodyAction(Request $request,Lease $lease)
    {
        $chalan = '';
        $chalanAmount=$lease->getApplications()[0]->getRegisterSix()[0]->getChalanAmount();
        $documents = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getApplication()->getId(),'entity'=>'application'));
        $regDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getApplications()[0]->getRegisterSix()[0]->getId(),'entity'=>'register'));
        $PublicDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Public'));
        $PrivateDocuments = $this->getDoctrine()->getRepository('LeaseBundle:Document')->findBy(array('refId'=>$lease->getId(),'entity'=>'lease','privacy' => 'Private'));
        $form = $this->createForm(new LeaseType($chalan,$chalanAmount), $lease);

        $surveys = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findAll();
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved'=>1,'deleted'=>0));

        if($request->getMethod()=="POST")
        {
            $form->handleRequest($request);
            if($form->isValid()) {
                $fiscaleYear = $form->get('fiscalyear')->getData();
                $lease->setStartDate(date_create("$fiscaleYear-04-14"));
                $fiscaleYear=$fiscaleYear+3;
                $lease->setEndDate(date_create("$fiscaleYear-04-13"));

                $lease->setStatus('ACTIVE');
                if(isset( $request->request->all()['demandFee'])) {

                    $totalAmount = $request->request->all()['demandFee'];
                    $lease->getApplications()[0]->setTotalAmount($totalAmount);
                }
                $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createPrevious($lease);
                $this->allDocumentUpload($request, $lease->getApplication());

                list($files, $file, $document, $uploaded) = $this->privateDocumentUpload($request, 'lease', $lease);
                $this->publicDocumentUpload($request, 'lease', $lease);
                $this->registerFileUpload($request, $lease->getApplications()[0]->getRegisterSix()[0]);

                $this->addFlash('success', 'লিজ সফলভাবে তৈরি করা হয়েছে');
                return $this->redirectToRoute('waiting_lease_list');
            }
        }

        return $this->render('@Lease/Lease/updatePreviousWaterLease.html.twig',array(
            'form' =>$form->createView(),
            'lease' =>$lease,
            'surveys' =>$surveys,
            'documents' =>$documents,
            'regDocuments' =>$regDocuments,
            'LeasePublicDocuments' =>$PublicDocuments,
            'LeasePrivateDocuments' =>$PrivateDocuments,
            'mouzas' =>$mouzas
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_PREVIOUS_LEASE_APPROVED")
     */
    public function statusChangeAction(Request $request,Lease $lease)
    {
        if(isset($_POST['approval'])) {
            $lease->setStatus('CLOSED');
        }else{
            $lease->setStatus('CORRECTION');
        }

        if($_POST['comment'] != '' ){

            $this->commentCreate($lease);
        }
        $this->addFlash('success', 'অবস্থা সফলভাবে পরিবর্তিত হয়েছে');
        $this->getDoctrine()->getRepository('LeaseBundle:Lease')->createLease($lease);

        return $this->redirectToRoute('waiting_lease_list');
    }

    /**
     * @param $lease
     */
    protected function commentCreate($lease) {

        $type = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $comment = new Comment();
        $comment->setCreatedDate(new \DateTime());
        $comment->setMessage($_POST['comment']);
        $comment->setUsersBy($this->getUser());
        $comment->setUserRole($type['name']);
        $comment->setRefId('LEASE_'.$lease->getId());
        $this->getDoctrine()->getRepository('LeaseBundle:Comment')->create($comment);

    }

    public function khatianLoadAction(Request $request,Mouza $mouza)
    {
        $survey = $_REQUEST['data'];
        $khatians = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Khatian')->khatianBymouza($mouza,$survey);

        $str = "";
        foreach ($khatians as $khatian) {

            $str .=  "<option value = $khatian[id]> $khatian[khatianNo]</option> ";
        }

        return new Response($str);
    }

    public function gadgetKhatianLoadAction(Mouza $mouza)
    {
        $khatians = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Khatian')->getSaRsKhatianBymouza($mouza);

        $khatianList = array(
            'SA' => "<option value=''>খতিয়ান নির্বাচন করুন</option>",
            'RS' => "<option value=''>খতিয়ান নির্বাচন করুন</option>",
        );

        foreach ($khatians as $khatian) {
            $khatianList[$khatian['surveyType']] .= "<option value = $khatian[id]> $khatian[khatianNo]</option>";
        }

        return new JsonResponse($khatianList);
    }


    /**
     * @JMS\Secure(roles="ROLE_RENEW_APPROVAL_ACCEPT")
     */
    public function renewApprovalAcceptAction(Request $request,Lease $lease)
    {
        $history = new History();
        $history->setStartDate($lease->getStartDate());
        $history->setEndDate($lease->getEndDate());
        $history->setLeaseId($lease);
        $history->setApplicationId($lease->getApplication());
        $this->getDoctrine()->getRepository('LeaseBundle:History')->save($history);
        $lease->setStatus('CLOSED');


        $startDate = clone $lease->getEndDate();
        $endDate = clone $lease->getEndDate();

        $lease->setStartDate($startDate->modify('+1 days'));
        $lease->setEndDate($endDate->modify('+1 years'));

        $this->getDoctrine()->getRepository('LeaseBundle:Lease')->save($lease);

        $this->addFlash('success', 'লিজ পুনঃনিবন্ধন অনুমোদন করা হয়েছে  ');
        return $this->redirectToRoute('approved_lease_list', array(
            'type' => 'Market',
        ));


    }
    /**
     * @param $lease
     */
    protected function RenewCommentCreate($lease) {

        $type = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserType($this->getUser());
        $comment = new Comment();
        $comment->setCreatedDate(new \DateTime());
        $comment->setMessage($_POST['comment']);
        $comment->setUsersBy($this->getUser());
        $comment->setUserRole($type['name']);
        $comment->setRefId('RENEW_'.$lease->getId());
        $this->getDoctrine()->getRepository('LeaseBundle:Comment')->create($comment);

    }

}
