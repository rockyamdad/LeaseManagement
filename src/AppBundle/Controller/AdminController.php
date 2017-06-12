<?php

namespace AppBundle\Controller;

use UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{

    public function panelAction()
    {
        return $this->render('AppBundle:Admin:panel.html.twig');
    }


    public function createUserAction(Request $request)
    {
        $data = [];

        if('POST' == $request->getMethod()) {
            $data = $request->request->all();
            $manager = $this->get('fos_user.user_manager');
            $helper = $this->get('fos_user.util.user_manipulator');

            $errors = $this->_validateUserData($data);
            if(count($errors) > 0) {
                $data['errors'] = $errors;
            } else {

                $pass = substr(md5(uniqid()), 0, 8);
                $user = $helper->create($data['username'], $pass, $data['email'], true, false);

                $user->addRole('ROLE_'. strtoupper($data['user_type']));
                $manager->updateUser($user);

                $this->_notifyUser($user, $data['user_type']);
                $this->addFlash('success','User created successfully.');
                return $this->redirect($this->generateUrl('users_list_all'));
            }
        }

        return $this->render('AppBundle:User:create.html.twig', $data);
    }

    private function _notifyUser(User $user, $role)
    {
        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $url = $this->get('router')->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);

        $message = \Swift_Message::newInstance()
                     ->setSubject('Welcome to ELRS')
                     ->setFrom([$this->container->getParameter('email.sender.email') => $this->container->getParameter('email.sender.name')])
                     ->setTo($user->getEmail())
                     ->setBody(
                         $this->renderView(
                             'email/registration.html.twig',
                             array('username' => $user->getUsername(), 'role' => ucfirst($role), 'resetUrl' => $url)
                         ),
                         'text/html'
                     );
        $this->get('mailer')->send($message);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);
    }

    private function _validateUserData($data)
    {
        $userTmp = new User();
        $userTmp->setUsername($data['username']);
        $userTmp->setEmail($data['email']);

        return $this->get('validator')->validate($userTmp);
    }

}
