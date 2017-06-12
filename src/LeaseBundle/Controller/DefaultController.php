<?php

namespace LeaseBundle\Controller;

use LeaseBundle\Entity\Application;
use LeaseBundle\Entity\Document;
use LeaseBundle\Entity\Gadget;
use LeaseBundle\Entity\Lease;
use LeaseBundle\Form\ApplicationType;
use LeaseBundle\Form\CitizenPasswordChangeType;
use LeaseBundle\Form\CitizenProfileEditType;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use LeaseBundle\Entity\CitizenUser;
use LeaseBundle\Form\CitizenLoginType;
use LeaseBundle\Form\CitizenRegistrationType;

class DefaultController extends BaseController
{
    public function indexAction(Request $request)
    {
        return $this->render('LeaseBundle:Frontend:index.html.twig');
    }
    public function leaseSearchAction(Request $request)
    {
        return $this->render('LeaseBundle:Frontend:index.html.twig');
    }
    public function phoneNumberVerificationCodeSendAction(Request $request)
    {
        $rand = rand(0,$request->request->get('phoneNumber'));
        $code= substr($rand,5);
        $session = new Session();
        $session->set('code', $code);
        $session->set('phone', $request->request->get('phoneNumber'));
        $smsText='Apnar jachai koron code'.' - '.$code;
        $this->get('sms.transporter')->send($request->request->get('phoneNumber'), $smsText);
        return new Response('true');
    }
    public function phoneNumberVerificationCodeConfirmationAction(Request $request)
    {
        $phone=$request->request->get('phoneNumber');
        $code = $request->request->get('code');
        $session = $request->getSession();

        if(($code == $session->get('code')) && ($phone == $session->get('phone'))){
            return new Response('true');
        }

        return new Response('false');
    }
    public function contactUsAction(Request $request)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            $message = \Swift_Message::newInstance()
                ->setSubject($data ['subject'])
                ->setFrom($data ['email'])
                ->setTo('pranab@emicrograph.com')
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        '@Lease/Frontend/emailFormate.html.twig',
                        array(
                            'fullName' => $data['fullName'],
                            'phoneNumber' => $data['phoneNumber'],
                            'email' => $data['email'],
                            'details' => $data['details']
                        )
                    ),
                    'text/html'
                )/*
                 * If you also want to include a plaintext version of the message
                ->addPart(
                    $this->renderView(
                        'Emails/registration.txt.twig',
                        array('name' => $name)
                    ),
                    'text/plain'
                )
                */
            ;
            $this->get('mailer')->send($message);
        }
        return $this->render('LeaseBundle:Frontend:contact_us.html.twig');
    }
    public function khatianShowAction(Request $request, Khatian $khatian)
    {
        $khatianView= $this->khatianVersion($request, $khatian->getLastVersion());
        return $this->render('@Lease/Frontend/khatianShow.html.twig', array('content' => $khatianView));
    }
    private function khatianVersion(Request $request, KhatianVersion $khatianVersion)
    {
        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatian = $khatianVersion->getKhatian();

        $khatianPageEntities = $khatianManager->getKhatianPages($khatianVersion);

        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);
        $khatianLog = $khatianManager->getKhatianLogByKhatianVersion($khatianVersion);

        return $this->renderView('@PorchaProcessing/Khatian/readonly_view_khatian.html.twig', array(
            'khatianPages'             => $khatianPages,
            'pagination'               => $pagination,
            'khatian'                  => $khatian,
            'khatianLog'               => $khatianLog,
            'query_params'             => $request->query->all(),
            'non_deliverables'         => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            'non_deliverable_template' => $this->renderView('@PorchaProcessing/Khatian/nondeliverable.html.twig', array('survey_name' => $khatian->getVolume()->getSurvey()->getName())),
            'khatian_versions'         => $khatianManager->getKhatianVersions($khatian),
            'this_version_id'          => $khatianVersion->getId()
        ));
    }

    public function portalPorchaSearchAction(Request $request)
    {
        $surveys = $this->get('porcha_processing.service.api_manager')->getSurveys();
        $mouzas =$this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('upozila'=>1,'approved' => 1));
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            try {
                $repositoryServiceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Khatian');
                $porchas  = $repositoryServiceRequest->getKhatian($data);
                if(!$porchas){
                    $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
            }

            return $this->render('@Lease/Frontend/porcha_search.html.twig', array(
                'porcha'   => $porchas,
                'surveys'   => $surveys,
                'mouzas' => $mouzas));
        }
        return $this->render('@Lease/Frontend/porcha_search.html.twig', array(
            'surveys'   => $surveys,
            'mouzas' => $mouzas));
    }
    public function leaseDetailsAction(Lease $lease)
    {
        $leaseDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $lease->getId(),'entity'=>'lease') );

        return $this->render('LeaseBundle:Frontend:leaseDetails.html.twig', array(
            'lease' => $lease,
            'leaseDocx' => $leaseDocx,
        ));
    }
    public function gadgetDetailsAction(Gadget $gadget, Lease $lease)
    {
        $gadgetDocx = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:Document')->findBy(array('refId'=> $gadget->getId(),'entity'=>'gadget') );

        return $this->render('LeaseBundle:Frontend:GadgetLeaseDetails.html.twig', array(
            'gadget' => $gadget,
            'lease' => $lease,
            'gadgetDocx' => $gadgetDocx,
        ));
    }
    public function leaseApplyAction(Request $request,Lease $lease, $type)
    {
        $application = new Application();
        $form = $this->createForm(new ApplicationType(), $application);
        if($request->getMethod()=="POST")
        {

            $form->handleRequest($request);

            if($form->isValid()) {
                $application->setLease($lease);
                $application->setStatus('PENDING');
                $applicationTrackingId = $this->createTrackingNo();
                $application->setApplicationTrackingId($applicationTrackingId);
                $application->getApplicant()->setApplication($application);
                $application->setCreatedDateTime(new \DateTime());
                $this->getDoctrine()->getRepository('LeaseBundle:Application')->create($application);
                $this->allDocumentUpload($request, $application);
                $this->addFlash('success', 'আপনার আবেদন সফল হয়েছে.');
                if($type == 'WaterBody' ){
                    return $this->redirectToRoute('water_body_lease_list_for_apply');
                }elseif($type == 'Market'){
                    return $this->redirectToRoute('market_body_lease_list_for_apply');
                }else{
                    return $this->redirectToRoute('gadget_lease_list_for_apply');
                }
            }
        }
        return $this->render('LeaseBundle:Frontend:leaseApplication.html.twig',array(
            'form' =>$form->createView(),
            'type' => $type
        ));
    }
    
    public function leaseApplicationStatusCheckAction(Request $request)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            $trackingId= $data['trackingId'];
            try {
                $application = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findOneBy(array('applicationTrackingId' => $trackingId));
                if(!$application){
                    $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
            }

            return $this->render('LeaseBundle:Frontend:application_status_check.html.twig',
                array(
                    'application' => $application
                )
            );
        }
        return $this->render('LeaseBundle:Frontend:application_status_check.html.twig');
        
    }
    public function citizenLeaseRenewCheckAction(Request $request)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            $trackingId= $data['trackingId'];
            $otp=$data['otp'];
            try {
                $application = $this->getDoctrine()->getRepository('LeaseBundle:Application')->findOneBy(array('applicationTrackingId' => $trackingId,'otp' =>$otp));
                if(!$application){
                    $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'আপনার রেকর্ড টি পাওয়া যাই নি .');
            }
            if ($application){
            return $this->redirectToRoute('citizen_lease_renew_application', array('application'=>$application->getId()));
            }
        }
        return $this->render('LeaseBundle:Frontend:lease_renew_check.html.twig');
        
    }
    public function citizenLeaseRenewApplicationAction(Request $request,Application $application)
    {
        if ($request->getMethod() == Request::METHOD_POST) {

            $application->getLease()->setStatus('WAITING_FOR_RENEW_APPROVAL');
            $application->setOtp(NULL);
            $type= $application->getLease()->getType();
            $this->allDocumentUpload($request, $application);
            $this->addFlash('success', 'লিজ পুনঃনিবন্ধন অনুমোদনের জন্য পাঠানো হয়েছে');
            return $this->redirectToRoute('lease_portal');

        }
        return $this->render('LeaseBundle:Frontend:lease_renew_application.html.twig',array(
            'application'=>$application
        ));
    }
    public function waterLeaseForApplyAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getWaterBodyLeaseListForApply();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Frontend:partial_portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Frontend:portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    public function marketLeaseForApplyAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getMarketLeaseListForApply();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Frontend:partial_portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Frontend:portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    public function gadgetLeaseForApplyAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 10);

        $allLeases = $this->getDoctrine()->getRepository('LeaseBundle:Lease')->getGadgetLeaseListForApply();
        $leases = $this->get('knp_paginator')->paginate($allLeases, $page, $perPage);

        if($request->isXmlHttpRequest()){
            return $this->render('LeaseBundle:Frontend:partial_portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
        else{
            return $this->render('LeaseBundle:Frontend:portal_open_lease_list_for_apply.html.twig',array(
                'leases'=>$leases
            ));
        }
    }
    public function doctrineManager()
    {
        return $this->getDoctrine()->getManager();
    }
    public function registrationAction(Request $request)
    {

        $user = new CitizenUser();
        $form =  $this->createForm(new CitizenRegistrationType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $password=md5($user->getPassword());
            $phoneNumber=$user->getPhoneNumber();
            $user->setPassword($password);
            $user->getProfile()->upload();
            $this->doctrineManager()->persist($user);
            $this->doctrineManager()->flush();
            $this->addFlash('success', 'সফলভাবে রেজিস্ট্রেশন সম্পন্ন হয়েছে');
//            $smsText = 'আপনি DRRS এ সফল ভাবে নিবন্ধিত হয়েছেন। আপনার ব্যবহারকারী আইডি নিজ মোবাইল নং, ধন্যবাদ ';
//            $this->get('app_bundle.service.sms_manager')->sendSms($phoneNumber, $smsText);
            return $this->redirect($this->generateUrl('portal_login'));
        }
        return $this->render('LeaseBundle:CitizenUser:citizenRegistration.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    
    public function logoutAction()
    {
        $session = new Session();
        $session->clear();
        return $this->redirect($this->generateUrl('lease_portal'));
    }
    
    public function loginAction(Request $request)
    {
        
        $form =  $this->createForm(new CitizenLoginType());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('LeaseBundle:CitizenUser');
            $userCheck = $repository->findOneBy(array(
                'phoneNumber' => $request->request->get('user_login')['phoneNumber'],
            ));
            if (isset($userCheck)) {
                $user = $repository->findOneBy(array(
                    'phoneNumber' => $request->request->get('user_login')['phoneNumber'],
                    'password' => md5($request->request->get('user_login')['password']),
                ));

                if (isset($user)) {
                    $phoneNumber = $user->getPhoneNumber();
                    $password = md5($user->getPassword());
                    $session = new Session();
                    $session->set('phoneNumber', $phoneNumber);
                    $session->set('password', $password);
                    $this->addFlash('success', 'সফলভাবে লগইন করা হয়েছে');
                    return $this->redirect($this->generateUrl('lease_portal'));
                } else {
                    $this->addFlash('error', 'আপনি ভুল পাসওয়ার্ড দিয়েছেন');
                }
            } else {
                $this->addFlash('error', 'আপনার ফোন নম্বরটি  রেজিস্টার  করা নাই ');
            }
        }

        return $this->render('LeaseBundle:CitizenUser:citizenLogin.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function profileAction(Request $request, $phoneNumber){

        $em=$this->getDoctrine()->getManager();
        $repository=$em->getRepository('LeaseBundle:CitizenUser');
        $user=$repository->findOneBy(array(
            'phoneNumber'=> $phoneNumber,
        ));
        return $this->render('LeaseBundle:CitizenUser:citizenProfile.html.twig', array(
            'user' => $user,
        ));
    }
    public function profileEditAction(Request $request, $phoneNumber){

        $em=$this->getDoctrine()->getManager();
        $repository=$em->getRepository('LeaseBundle:CitizenUser');
        $user=$repository->findOneBy(array(
            'phoneNumber'=> $phoneNumber,
        ));
        $form =  $this->createEditForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $user->getProfile()->upload();
            $this->doctrineManager()->persist($user);
            $this->doctrineManager()->flush();
            return $this->redirect($this->generateUrl('lease_portal'));
        }
        return $this->render('LeaseBundle:CitizenUser:citizenProfileEdit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
    private function createEditForm(CitizenUser $entity)
    {
        $form = $this->createForm(new CitizenProfileEditType(), $entity, array(
            'action' => $this->generateUrl('portal_profile_edit', array('phoneNumber' => $entity->getPhoneNumber())),
            'method' => 'PUT',
        ));

        return $form;

    }
    public function passwordChangeAction(Request $request,$phoneNumber)
    {
        $session = new Session();
        $em=$this->getDoctrine()->getManager();
        $repository=$em->getRepository('LeaseBundle:CitizenUser');
        $user=$repository->findOneBy(array(
            'phoneNumber'=> $phoneNumber,
        ));
        $form =  $this->createForm(new CitizenPasswordChangeType(), $user);
        $form->handleRequest($request);
        if($session->get('phoneNumber')){
            if ($form->isValid()) {
                $user = $repository->findOneBy(array(
                    'phoneNumber' => $phoneNumber,
                    'password' => md5($request->request->all()['user_password_change']['Password']),
                ));
                if (isset($user)) {
                    $password = md5($user->getPassword());
                    $user->setPassword($password);
                    $this->doctrineManager()->persist($user);
                    $this->doctrineManager()->flush();
                    $this->addFlash('success', 'আপনার পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে');
                    return $this->redirect($this->generateUrl('citizen_homepage'));
                }else{
                    $this->addFlash('error', 'আপনার পুরাতন পাসওয়ার্ড টি ভুল দেওয়া হয়েছে ');
                }
            }
        }
        else{
            $this->addFlash('error', 'আপনি লগইন নেই');
        }
        return $this->render('LeaseBundle:CitizenUser:citizenPasswordChange.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    public function forgetPasswordAction(Request $request)
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            try {
                $phoneNumber = $data['mobile'];
                $hasUser = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:CitizenUser')->findOneByPhoneNumber($phoneNumber);
                if($hasUser){
                    $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $session = new Session();
                    $session->set('verificationCode', $verificationCode);
                    $session->set('time', time());
                    $smsText = 'যাচাইকরণ কোড - '.$verificationCode.' ';
                    $this->get('app_bundle.service.sms_manager')->sendSms($phoneNumber, $smsText);
                    $this->addFlash('success', 'আপনার ফোন নম্বর এ যাচাইকারী কোড পাঠানো হয়েছে ');
                    return $this->redirectToRoute('portal_forget_password_verification',array('phoneNumber'=>$phoneNumber));
                }
                else{
                    $this->addFlash('error', 'আপনার ফোন নম্বরটি  রেজিস্টার  করা নাই ');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'আপনার আবেদন সফল ছিল না.');
            }
        }
        return $this->render('LeaseBundle:CitizenUser:citizenForgetPassword.html.twig', array(

        ));
    }
    public function forgetPasswordVerificationAction(Request $request,$phoneNumber)
    {

        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            try {
                $hasUser = $this->getDoctrine()->getManager()->getRepository('LeaseBundle:CitizenUser')->findOneByPhoneNumber($phoneNumber);
                if($hasUser){
                    $session = new Session();
                    if ((time() - $session->get('time')) > (60 * 5)) {
                        $session->clear();
                        $this->addFlash('error', 'আপনার যাচাইকারী কোডটির মেয়াদ শেষ ');
                        return $this->redirectToRoute('portal_forget_password');
                    }
                    $qDecoded=$session->get('verificationCode');
                    if($data['verificationCode']==$qDecoded) {
                        $randomPassword = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        $hasUser->setPassword(md5($randomPassword));
                        $this->doctrineManager()->persist($hasUser);
                        $this->doctrineManager()->flush();
                        $smsText = 'আপনার নতুন পাসওয়ার্ড হচ্ছে - ' . $randomPassword . ' ';
                        $this->get('app_bundle.service.sms_manager')->sendSms($phoneNumber, $smsText);
                        return $this->redirectToRoute('portal_login');
                    }
                    else{
                        $this->addFlash('error', 'আপনার যাচাইকারী কোড টি সঠিক নয়');
                    }
                }

            } catch (\Exception $e) {
                $this->addFlash('error', 'আপনার আবেদন সফল ছিল না.');
            }
        }
        return $this->render('LeaseBundle:CitizenUser:citizenForgetPasswordVerification.html.twig', array(

        ));
    }
    
    
}
