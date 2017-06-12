<?php

namespace UserBundle\Controller;

use JMS\SecurityExtraBundle\Annotation as JMS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\Type\AssignRoleToNessUserForm;
use UserBundle\Form\Type\AssignUserToOfficeForm;
use UserBundle\Form\Type\UserUpdatePasswordForm;

/**
 * User Controller.
 *
 */
class UserController extends Controller
{
    /**
     * @Route("/users", name="users_home")
     * @Template()
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 100);

        $users = $this->getDoctrine()->getRepository('RbsUserBundle:User')->getUserList($request->query->all(), $this->getUser());
        $data['users'] = $this->get('knp_paginator')->paginate($users, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            $response = $this->render('RbsUserBundle:User:list_content.html.twig', $data);
        } else {
            $data['groups'] = $this->getDoctrine()->getRepository('RbsUserBundle:Group')->getGroupOfOwnOffice($this->getUser());
            $response = $this->render('RbsUserBundle:User:index.html.twig', $data);
        }

        return $response;
    }

    /**
     * @Route("/user/create", name="user_create")
     * @Template("RbsUserBundle:User:new.html.twig")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function createAction(Request $request)
    {
        $user = new User();

        $service = $this->get('rbs_user.registration.form.type');
        $service->setLoginUser($this->getUser());
        $form = $this->createForm($service, $user);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $user->setEnabled(true);
                $user->setOffice($this->getUser()->getOffice());

                if ($this->getUser()->getOffice()->getType() == 'MINISTRY' && $user->getGroups()[0]->getType() != 'MINISTRY') {
                    $user->setOfficeAdmin(true);
                    $user->setOffice(null);
                }

                $this->getDoctrine()->getRepository('RbsUserBundle:User')->create($user);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('User Created Successfully!')
                );

                return $this->redirect($this->generateUrl('users_home'));
            }
        }

        return array(
            'form' => $form->createView(),
            'mode' => 'create',
            'user' => $user
        );
    }

    /**
     * @Route("/user/update/{id}", name="user_update", options={"expose"=true})
     * @Template("RbsUserBundle:User:new.html.twig")
     * @param Request $request
     * @param User $user
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function updateAction(Request $request, User $user)
    {
        if (!$this->hasOfficeAccess($user) || $user->isNessUser()) {
            return $this->redirectToUserHomeWithError();
        }

        //$form = $this->createForm(new UserUpdateForm(), $user);
        $service = $this->get('rbs_user.registration.form.type');
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

                return $this->redirect($this->generateUrl('users_home'));
            }
        }

        return array(
            'form' => $form->createView(),
            'mode' => 'edit',
            'user' => $user
        );
    }

    /**
     * @Route("/user/update/password/{id}", name="user_update_password", options={"expose"=true})
     * @Template("RbsUserBundle:User:update.password.html.twig")
     * @param Request $request
     * @param User $user
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function updatePasswordAction(Request $request, User $user)
    {
        $form = $this->createForm(new UserUpdatePasswordForm(), $user);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $user->setPassword($form->get('plainPassword')->getData());
                $user->setPlainPassword($form->get('plainPassword')->getData());

                $this->getDoctrine()->getRepository('RbsUserBundle:User')->update($user);

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    $this->get('translator')->trans('Password Successfully Change')
                );

                return $this->redirect($this->generateUrl('users_home'));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/user/enabled/{id}", name="user_enabled", options={"expose"=true})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function userEnabledAction(User $user)
    {
//        if (!$this->hasOfficeAccess($user)) {
//            return $this->redirectToUserHomeWithError();
//        }
        $enable = $this->isUserEnabled($user);
        $user->setEnabled($this->isUserEnabled($user));

        $this->getDoctrine()->getRepository('RbsUserBundle:User')->update($user);

        $messageString = $enable ? $this->get('translator')->trans('User Successfully Enable') : $this->get('translator')->trans('User Successfully Disable');
        $this->get('session')->getFlashBag()->add(
            'success',
            $messageString
        );

        return $this->redirect($this->generateUrl('users_home'));
    }

    /**
     * @Route("/user/details/{id}", name="user_details", options={"expose"=true})
     * @Template()
     * @param User $user
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function detailsAction(User $user)
    {
        return $this->render('RbsUserBundle:User:details.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete", options={"expose"=true})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function deleteAction(User $user)
    {
        /*if($user->getProfile()->getPath()){
            $user->getProfile()->removeFile($user->getProfile()->getPath());
        }

        $this->getDoctrine()->getRepository('RbsUserBundle:User')->delete($user);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('User Successfully Delete')
        );*/

        return $this->redirect($this->generateUrl('users_home'));
    }

    /**
     * @Route("/profile/{id}", name="user_profile")
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function profileAction(User $user, Request $request)
    {
        $data = ['user' => $user, 'self' => false];

        if($this->getUser() == $user) {
            $data['self'] = true;
            $data['form'] = $this->get('fos_user.change_password.form.factory')->createForm()->createView();
        }

        if('POST' == $request->getMethod() && $data['self']) {
            $post = $request->request->get('fos_user_change_password_form');
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(), $post['current_password'], $user->getSalt())) {
                if($post['plainPassword']['first'] == $post['plainPassword']['second']) {
                    $manager = $this->get('fos_user.user_manager');
                    $user->setPlainPassword($post['plainPassword']['first']);
                    $manager->updateUser($user);

                    $this->addFlash('success',$this->get('translator')->trans('Password changed successfully!'));
                } else {
                    $this->addFlash('error',$this->get('translator')->trans('Password and Varification didn\'t match. Password not changed.'));
                }
            } else {
                $this->addFlash('error',$this->get('translator')->trans('Old password incorrect! Password not changed.'));
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('RbsUserBundle:User:profile_modal.html.twig', $data);
        }

        return $this->render('RbsUserBundle:User:profile.html.twig', $data);
    }
    /**
     * @Route("/password-generated/{id}", name="user_password_generated")
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function userPasswordRegeneratedAction(User $user)
    {
        $userCellPhoneNumber = $user->getProfile()->getCellphone();

        $manager = $this->get('fos_user.user_manager');
        $smsService = $this->get('sms.transporter');

        $newPassword = rand(100000,999999);

        $countryCode = $this->checkPhoneNumberWithCountryCode("$userCellPhoneNumber");

        if($countryCode) {
            $smsService->send($userCellPhoneNumber,'apnar notun passwordti holo '.$newPassword);
        }  else {
            $smsService->send('+88'.$userCellPhoneNumber,'apnar notun passwordti holo '.$newPassword);
        }

        $user->setPlainPassword($newPassword);
        $manager->updateUser($user);
        $this->addFlash('success',$this->get('translator')->trans('password sent to users mobile'));
        return $this->redirect($this->generateUrl('users_home'));
    }

    private function checkPhoneNumberWithCountryCode($userCellPhoneNumber){

        preg_match( '/(0|\+?\d{2})(\d{7,8})/', $userCellPhoneNumber, $matches);

        if($matches[1] == '+88'){
            return $matches[1];
        } else{
            return false;
        }

    }

    private function hasOfficeAccess(User $user)
    {
        /** @var User $loginUser */
        $loginUser = $this->getUser();
        if (in_array($loginUser->getOffice()->getType(), array('DC', 'AC_LAND', 'UDC')) && $loginUser->getOffice()->getId() != $user->getOffice()->getId()) {
            return false;
        }

        return true;
    }

    private function redirectToUserHomeWithError()
    {
        $this->get('session')->getFlashBag()->add(
            'error',
            'Permission Denied!'
        );

        return $this->redirect($this->generateUrl('users_home'));
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

    /**
     * @Route("/user/{id}/assign-to-office", name="assign_user_to_office")
     * @Template("RbsUserBundle:User:_assign_to_office.html.twig")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function assignToOfficeAction(Request $request, User $user)
    {
        if (!in_array($user->getType(), array('DC', 'AC_LAND', 'UDC'))) {
            return new Response('');
        }

        $form = $this->createForm(new AssignUserToOfficeForm($user), $user, array(
            'action' => $this->generateUrl('assign_user_to_office', array('id' => $user->getId())),
            'method' => 'POST',
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $translator = $this->get('translator');
                $message = $user->getOffice() ? $translator->trans('User has been assigned as admin.') . ' ' . $user->getOffice()->getName() : $translator->trans('Office removed from user.');
                $this->addFlash('success', $message);

                return $this->redirect($this->generateUrl('users_home'));
            }
        }

        return array(
            'user' => $user,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/user/{id}/assign-role-to-ness-user", name="assign_role_to_ness_user")
     * @Template("RbsUserBundle:User:_assign_role_to_ness_user.html.twig")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_MANAGE_USERS")
     */
    public function assignRoleToNessUserAction(Request $request, User $user)
    {
        if (!$user->isNessUser()) {
            return new Response('');
        }

        $form = $this->createForm(new AssignRoleToNessUserForm($user), $user, array(
            'action' => $this->generateUrl('assign_role_to_ness_user', array('id' => $user->getId())),
            'method' => 'POST',
        ));

        $form->get('groups')->setData($user->getGroups()[0]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setGroups(array($form->get('groups')->getData()));
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', $this->get('translator')->trans('Update Role Successfully'));

                return $this->redirect($this->generateUrl('users_home'));
            }
        }

        return array(
            'user' => $user,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/user/check/username", name="check_username")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function checkUsernameAction(Request $request)
    {
        $username = $request->query->get('user[username]', null, true);
        $manager = $this->get('fos_user.user_manager');
        $response = $manager->findUserByUsername($username) ? 'false' : 'true';

        return new Response($response);
    }

    /**
     * @Route("/user/check/email", name="check_email")
     * @param Request $request
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function checkEmailAddresAction(Request $request)
    {
        $email = $request->query->get('user[email]', null, true);
        $manager = $this->get('fos_user.user_manager');
        $response = $manager->findUserByEmail($email) ? 'false' : 'true';

        return new Response($response);
    }

    /**
     * @Route("/user/{id}/tooltip-info", name="user_tooltip_info")
     * @param Request $request
     */
    public function userInfoAction(Request $request, User $user) {

        return $this->render('RbsUserBundle:User:tooltip_info.html.twig', array('user' => $user));
    }
}