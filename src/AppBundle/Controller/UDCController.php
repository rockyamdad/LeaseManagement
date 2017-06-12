<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Document;
use AppBundle\Entity\Office;
use AppBundle\Entity\Udc;
use AppBundle\Entity\UdcEntrepreneur;
use AppBundle\Form\UdcType;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Validator\Constraints\File;
use UserBundle\Entity\Profile;
use UserBundle\Entity\User;

class UDCController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_APP_UPOZILAS")
     */
    public function udcListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $udcLists = $this->get('app.service.udc_manager')->getUdcList($request->query->all());
        $data['udcLists']  = $this->get('knp_paginator')->paginate($udcLists, $page, 50);
        $data['districts'] = $this->get('app.service.udc_manager')->getRelatedDistricts();

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Udc:udc_list_sub.html.twig', $data);
        } else {
            return $this->render('AppBundle:Udc:udc_list.html.twig', $data);
        }

    }

    public function addUdcAction(Request $request)
    {
        $udc = new Udc();
        $status = 'insert';
        $form = $this->createForm(new UdcType($status), $udc);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.udc_manager')->createUdc($udc);


                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Udc Successfully Updated'
                );
                return $this->redirect($this->generateUrl('udc_list'));
            }
        }
        return $this->render(
            'AppBundle:Udc:addForm.html.twig',
            array(
                'form'     => $form->createView(),
                'udc'      => ''
            )
        );
    }

    public function updateUdcAction(Request $request,Udc $udc)
    {
        $status = 'update';
        $form = $this->createForm(new UdcType($status), $udc);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('app.service.udc_manager')->createUdc($udc);


                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Udc Successfully Updated'
                );
                return $this->redirect($this->generateUrl('udc_list'));
            }
        }
        return $this->render(
            'AppBundle:Udc:editForm.html.twig',
            array(
                'form'     => $form->createView(),
                'udc'      =>$udc
            )
        );
    }

    public function duplicateUsernameCheckAction(Request $request)
    {
        $username = $request->request->get("udc[user][username]", NULL, TRUE);

        $duplicateUserName =  $this->getDoctrine()->getRepository('RbsUserBundle:User')->findByUsername($username);

       if ($duplicateUserName && $duplicateUserName[0]->getId() != $request->request->get("user_id")) {
        $response = 'false';
    } else {
        $response = 'true';
    }
       return new Response($response);
   }

    public function udcUserGetInformationAction()
    {
        $testApi = $this->get('app.service.udc_api_manager');
         $data = $testApi->udcUserGetInformation();

        return new Response($data);
    }



    public function udcUserCreateAction()
    {
        # /udc-respone-api
        $client   = $this->get('guzzle.client.api_crm');
        $response = $client->get('/udc-api');

        if ($response->getStatusCode() == 200) {
            $string = (string) $response->getBody();

            $data = json_decode($string);

            if (!json_last_error()) {
                 $this->createOffice($data->offices);
                 $this->createUsers($data->users);
            }
        }

        return new Response();
    }

    private function createOffice($offices) {
        foreach ($offices as $row) {

            $duplicateOffice = $this->getDoctrine()->getRepository('AppBundle:Office')->findBy(array('name' =>$row->name));

            if(empty($duplicateOffice)) {

                $office = new Office();
                $office->setName($row->name);
                $office->setType('UDC');
                $office->setactive($row->active);
                $office->setRelatedDistricts(trim($row->districtGeocode));

                $district = $this->getDoctrine()->getRepository('AppBundle:District')->findOneBy(
                    array('geocode' => trim($row->districtGeocode))
                );
                $parent = $this->getDoctrine()->getRepository('AppBundle:Office')->findOneBy(
                    array('district' => $district, 'type' => 'DC')
                );
                $office->setParent($parent);
                $office->setDistrict($district);

                $upozila = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findOneBy(
                    array('name' => trim($row->upozila))
                );
                $office->setUpozila($upozila);

                $this->getDoctrine()->getManager()->persist($office);
            }
        }
        $this->getDoctrine()->getManager()->flush();
    }


    private function createUsers($users)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        foreach ($users as $row) {

            $checkUsername = $userManager->findUserByUsername($row->username);
            if(empty($checkUsername)){

                $user    = new User();
                $profile = new Profile();

                $profile->setUser($user);
                $user->setUsername($row->username);
                $user->setEmail($row->email);
                $user->setPlainPassword(123456);
                $user->setEnabled(1);
                $user->setProfile($profile);

                $group = $this->getDoctrine()->getRepository('RbsUserBundle:Group')->findOneBy(array('name' => 'ইউ ডি সি এডমিন'));
                if ($group) {
                    $user->setGroups(array($group));
                }

                $office = $this->getDoctrine()
                               ->getManager()
                               ->getRepository('AppBundle:Office')
                               ->findOneBy(array('name'=>$row->office));
                $user->setOffice($office);
                $userManager->updateUser($user);
            }
        }
    }

    public function udcTypeOfficesCreateAction()
    {
        # /udc-office-api
        $client   = $this->get('guzzle.client.api_crm');
        $response = $client->get('/udc-office-api');

        if ($response->getStatusCode() == 200) {
            $string = (string) $response->getBody();

            $data = json_decode($string);
            if (!json_last_error()) {
                $this->createUdcOffice($data);
                 $this->createUdcUsers($data);
            }
        }

        return new Response();
    }

    public function udcOfficeGetInformationAction()
    {

        $testApi = $this->get('app.service.udc_api_manager');
        $data = $testApi->udcTypeOfficesImport();

        return new Response($data);
    }

    private function createUdcOffice($offices) {

        foreach ($offices as $row) {

            $duplicateOffice = $this->getDoctrine()->getRepository('AppBundle:Office')->findBy(array('name' =>$row->name));

            if(empty($duplicateOffice)) {

                $office = new Office();
                $office->setName($row->name);
                $office->setType('UDC');
                $office->setactive($row->active);
                $office->setRefId($row->refId);

                $office->setRelatedDistricts(trim($row->districtGeocode));

                $district = $this->getDoctrine()->getRepository('AppBundle:District')->findOneBy(
                    array('geocode' => trim($row->districtGeocode))
                );
                $office->setDistrict($district);

                $upozila = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findOneBy(
                    array('geocode' => trim($row->upozila))
                );
                $union = $this->getDoctrine()->getRepository('AppBundle:Union')->findOneBy(
                    array('geocode' => trim($row->union))
                );

                $office->setUpozila($upozila);
                $office->setUnion($union);

                $this->getDoctrine()->getManager()->persist($office);
            }
        }
        $this->getDoctrine()->getManager()->flush();
    }

    private function createUdcUsers($users)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        foreach ($users as $row) {

            $checkUsername = $userManager->findUserByUsername($row->refId);

            if(empty($checkUsername)){

                $user    = new User();
                $profile = new Profile();

                $profile->setUser($user);
                $user->setUsername($row->refId);
                $user->setEmail($row->refId.'@cc.com');
                $user->setPlainPassword(123456);
                $user->setEnabled(0);
                $user->setProfile($profile);

                $group = $this->getDoctrine()->getRepository('RbsUserBundle:Group')->findOneBy(array('name' => 'ইউ ডি সি এডমিন'));
                if ($group) {
                    $user->setGroups(array($group));
                }

                $office = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('AppBundle:Office')
                    ->findOneBy(array('name'=>$row->name));
                $user->setOffice($office);
                $userManager->updateUser($user);
            }
        }
    }

    public function udcLoginAction(Request $request) {

        if ('POST' == $request->getMethod()) {
                $isLogin = $this->udcApiLoginRequest($request->request->all(),$request);
            if($isLogin){
            return $this->redirect($this->generateUrl('dashboard'));
            }
        }
        return $this->render('AppBundle:Udc:Udclogin.html.twig');
    }

    public function udcApiLoginRequest($userInfo,$request){

        $loginInfo = $this->get('app.service.udc_api_manager');
        $response = $loginInfo->udcLoginInfoResponse($userInfo);

        $string = (string) $response;

        $data = json_decode($string);

        if (!json_last_error()) {
            if($userInfo['_username'] == $data[0]->refId){

                $userManager = $this->container->get('fos_user.user_manager');
                $user = $userManager->findUserByUsername($userInfo['_username']);

                $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());

                // For older versions of Symfony, use security.context here
                $this->get("security.token_storage")->setToken($token);

                // Fire the login event
                // Logging the user in above the way we do it doesn't do this automatically
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                return true;
            } else{
                return false;
            }
        }

    }

    public function udcApiUserLoginInfoAction(){
        $users = [['refId'=>'judcoffice1']];
          $jsonUdcTypeOfficesData = json_encode($users);
        return new Response($jsonUdcTypeOfficesData);
    }

    public function udcOfficeAndUserCreateAction(Request $request)
    {

        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                try {


                    $lists = $this->get('app.model.data_import_manager')
                        ->importDataIntoDivisionDistrictUpozillaUnions($document, $app = true);

                }catch (\Exception $e){
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'সি. এস. ভি ফাইল আবার পর্যবেক্ষণ করুন '
                    );
                }
                if(!empty($lists)){

                    foreach($lists as $list){

                        $office = $this->udcOfficeCreate($list);

                        if($office){

                        $this->udcUserCreate($list,$office);

                        }
                    }

                    return $this->redirect($this->generateUrl('udc_office_list'));
                }
            }
        }
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('app_division_district_upzilla_list');
        return $this->render('AppBundle:Udc:udc_import.html.twig', $data);

    }

    private function createImportForm($document)
    {
        return $this->createFormBuilder($document)
            ->add('file', 'file', array(
                'required' => true,
                'constraints' => array(
                    new File(array(
                        'mimeTypes' => array(
                            'application/vnd.ms-excel',
                            'text/plain',
                            'text/csv',
                            'text/tsv'
                        ),
                        'mimeTypesMessage' => 'Only CSV files are allowed'
                    ))
                )
            ))
            ->getForm();
    }

    public function udcOfficeCreate($list){

        $duplicateOffice = $this->getDoctrine()->getRepository('AppBundle:Office')->findBy(array('name' =>$list['officeName']));

        if(empty($duplicateOffice)) {

            $office = new Office();
            $office->setName(trim($list['officeName']));
            $office->setType('UDC');
            $office->setactive(false);

            $office->setRelatedDistricts(trim($list['districtGeocode']));

            $district = $this->getDoctrine()->getRepository('AppBundle:District')->findOneBy(
                array('geocode' => trim($list['districtGeocode']))
            );
            $parent = $this->getDoctrine()->getRepository('AppBundle:Office')->findOneBy(
                array('district' => $district, 'type' => 'DC')
            );
            $office->setParent($parent);

            $office->setDistrict($district);

            $upozila = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findOneBy(
                array('geocode' => trim($list['upozilaGeocode']))
            );
            $union = $this->getDoctrine()->getRepository('AppBundle:Union')->findOneBy(
                array('geocode' => trim($list['unionGeocode']))
            );

            $office->setUpozila($upozila);
            $office->setUnion($union);

            $this->getDoctrine()->getManager()->persist($office);
//            $this->getDoctrine()->getManager()->flush();
            return $office;
        }
    }

    public function udcUserCreate($lists,Office $office){

        $userManager = $this->container->get('fos_user.user_manager');
        $checkUsername = $userManager->findUserByUsername($lists['username']);

        if(empty($checkUsername)){


            $user    = new User();
            $profile = new Profile();

            $profile->setUser($user);
            $profile->setCellphone($lists['mobileNumber']);
            $profile->setCurrentAddress($lists['address']);
            $profile->setPermanentAddress($lists['address']);
            $profile->setFullNameBn($lists['fullName']);
            $profile->setGender($lists['gender']);
            $user->setUsername($lists['username']);
            $user->setEmail($lists['email']);
            $user->setPlainPassword($lists['password']);
            $user->setEnabled(0);
            $user->setProfile($profile);

            $group = $this->getDoctrine()->getRepository('RbsUserBundle:Group')->findOneBy(array('name' => 'ইউ ডি সি এডমিন'));
            if ($group) {
                $user->setGroups(array($group));
            }

            /*$office = $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Office')
                ->findOneBy(array('name'=>$lists['officeName']));*/

            $user->setOffice($office);

            $userManager->updateUser($user);

        }

    }
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function udcUserListAction(Request $request)
    {
        $udcOffices = $this->getUdcOffices();
        $district = $this->getUser()->getOffice()->getDistrict() ? $this->getUser()->getOffice()->getDistrict()->getId():null;
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $users = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUdcUserList($request->query->all(), $this->getUser(), $udcOffices);

        $data['users'] = $this->get('knp_paginator')->paginate($users, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            $response = $this->render('AppBundle:Udc:user_list_content.html.twig', $data);
        } else {
            $data['upozilas'] = $this->getDoctrine()->getRepository('AppBundle:Upozila')->findBy(array('approved' => 1,'district'=>$district));
            $response = $this->render('AppBundle:Udc:user_list.html.twig', $data);
        }

        return $response;
    }

    public function udcUserStatusAction(Request $request) {

        if (!$this->isCsrfTokenValid('udc_user_move_action', $request->request->get('_token'))) {
            $this->addFlash('error', $this->get('translator')->trans('token incorrect'));
            return new Response();
        }
        $requestAll = $request->request->all();

        if (isset($requestAll['chk'])) {
            $ids = $request->request->get('chk');
            $status = $request->request->get('selectedChk');
            $this->udcUserStatusChanges($status,$ids);
            $this->addFlash('success', $this->get('translator')->trans('work done'));
            return $this->redirect($this->generateUrl('udc_user_list'));
        }
        $this->addFlash('error', $this->get('translator')->trans('nothing done'));

        return new Response();
    }

    public function udcUserStatusChanges($status,$ids) {

        if (count($ids) < 1) {
            return;
        }
        $UserManager = $this->getDoctrine()->getRepository('RbsUserBundle:User');

        /** @var User $users */
        foreach ($ids as $key => $val) {
            $user = $UserManager->find($key);
            $user->setEnabled($status);
            $this->getDoctrine()->getRepository('RbsUserBundle:User')->update($user);
        }
    }

    public function udcUserUpdateAction(Request $request, User $user)
    {
        /*if (!$this->hasOfficeAccess($user) || $user->isNessUser()) {
            return $this->redirectToUserHomeWithError();
        }*/

        //$form = $this->createForm(new UserUpdateForm(), $user);
        $service = $this->get('udc_user.registration.form.type');
        $service->setLoginUser($this->getUser());
        $form = $this->createForm($service, $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('fos_user.user_manager')->updateUser($user);
                $user->getProfile()->preUpload();
                $user->getProfile()->upload();
                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('User Updated Successfully!')
                );

                return $this->redirect($this->generateUrl('udc_user_list'));
            }
        }
        return $this->render(
            'AppBundle:Udc:udcNewUser.html.twig',
            array(
                'form' => $form->createView(),
                'mode' => 'edit',
                'user' => $user
            )
        );

    }

    public function userEnabledAction(User $user)
    {

        $enable = $this->isUserEnabled($user);
        $user->setEnabled($this->isUserEnabled($user));

        $this->getDoctrine()->getRepository('RbsUserBundle:User')->update($user);

        $messageString = $enable ? $this->get('translator')->trans('User Successfully Enable') : $this->get('translator')->trans('User Successfully Disable');
        $this->get('session')->getFlashBag()->add(
            'success',
            $messageString
        );

        return $this->redirect($this->generateUrl('udc_user_list'));
    }
    /**
     * @param User $user
     * @return int
     */
    protected function isUserEnabled(User $user)
    {
        if ($user->isEnabled()) {
            return false;
        } else {
            return true;
        }
    }

    public function getUdcOffices() {

        $office = $this->getUser()->getOffice();

        /**@var Office $office*/
        $offices = array();
        if ($office->getChildren()) {
            foreach ($office->getChildren() as $children) {
                $offices[] = $children->getId();
            }
        }
        return $offices;
    }

}
