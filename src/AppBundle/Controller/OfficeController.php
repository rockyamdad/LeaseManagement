<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Office;
use AppBundle\Entity\OfficeSettings;
use AppBundle\Form\ACLandOfficeType;
use AppBundle\Form\DCOfficeType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class OfficeController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_OFFICES")
     */
    public function updateDCOfficeAction(Request $request, Office $office_ = null)
    {
        $office = new Office();
        if ($office_) {
            $office = $office_;
        }
        $form = $this->createForm(new DCOfficeType(($office->getId()) ? $office->getDistrict() : null), $office);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $modules = $request->request->get('modules');
                $office->setModules($modules);
                $this->get('app.service.office_manager')->updateDCOffice($office);
                return $this->redirect($this->generateUrl('office_list'));
            }
        }

        $data['office'] = $office;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_dc_office');
        if ($office->getId()) {
            $data['form_action'] = $this->generateUrl('update_dc_office', array('id' => $office->getId()));
            $data['title'] = 'Update DC Office';
        }

        return $this->render('AppBundle:Office:update_dc_office.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_OFFICES")
     */
    public function updateACLandOfficeAction(Request $request, Office $office_ = null)
    {

        $office = new Office();
        if ($office_) {
            $office = $office_;
        }

        $form = $this->createForm(new ACLandOfficeType(), $office);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.service.office_manager')->updateACLandOffice($office);
                return $this->redirect($this->generateUrl('office_list', array('type' => $office->getType())));
            }
        }

        $data['office'] = $office;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_ac_land_office');
        if ($office->getId()) {
            $data['form_action'] = $this->generateUrl('update_ac_land_office', array('id' => $office->getId()));
            $data['title'] = 'Update AC Land Office';
        }

        return $this->render('AppBundle:Office:update_ac_land_office.html.twig', $data);
    }
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_OFFICES,ROLE_MENU_ITEM_USER")
     */
    public function officeListAction(Request $request, $type = 'DC') {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 50);
        $data = array();

        $offices = $this->get('app.service.office_manager')->getOfficeList($type, $request->query->all());
        $data['offices']  = $this->get('knp_paginator')->paginate($offices, $page, $perPage);
        $data['type'] = $type;

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Office:office_list_sub.html.twig', $data);
        } else {
            $data['title'] = $this->get('translator')->trans($this->officeTitle($type));
            $data['districts'] = $this->getDoctrine()->getRepository('AppBundle:District')->findBy(array('approved' => 1));
            $data['search_url'] = $this->generateUrl('office_list', array('type' => $type));
            return $this->render('AppBundle:Office:office_list.html.twig', $data);
        }
    }
    /**
     * @JMS\Secure(roles="ROLE_MANAGE_OFFICES,ROLE_MENU_ITEM_USER")
     */
    public function udcOfficeListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);
        $data = array();

        $offices = $this->get('app.service.office_manager')->getUdcOfficeList($request->query->all());
        $data['offices']  = $this->get('knp_paginator')->paginate($offices, $page, $perPage);
        $data['type'] = 'UDC';

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Office:udc_office_list_sub.html.twig', $data);
        } else {
            $data['title'] = $this->get('translator')->trans($this->officeTitle($data['type']));
            $data['districts'] = $this->getDoctrine()->getRepository('AppBundle:District')->findBy(array('approved' => 1));
            $data['search_url'] = $this->generateUrl('office_list', array('type' => $data['type']));
            return $this->render('AppBundle:Office:udc_office_list.html.twig', $data);
        }
    }

    private function officeTitle($type) {

        switch (strtoupper($type)) {
            case 'DC':
                return 'DC Office';
            case 'AC_LAND':
                return 'AC Land Office';
            case 'UDC':
                return 'UDC Office';
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_MANAGE_OFFICES,ROLE_MENU_ITEM_USER")
     */
    public function enableOfficeAction(Office $office) {

        $em = $this->getDoctrine()->getEntityManager();

        $users =  $this->getDoctrine()
            ->getRepository('RbsUserBundle:User')
            ->findByOffice($office);

        if($office->isActive()){
            $this->officeUserDeactive($users, $em);
        }else {
            $this->onlyAdminUsersActive($users, $em);
        }
        $office->setActive(!$office->isActive());
        $em->persist($office);
        $em->flush();
        return $this->redirect($this->generateUrl('office_list', array('type' => strtolower($office->getType()))));
    }

    public function officeUpozilasAction(Office $office) {

        $rows = $this->get('app.service.office_manager')->getOfficeUpozilas($office);

        $data = array();
        foreach ($rows as $row) {
            $data [] = '<option value="' . $row->getId() . '">' . $row->getName() . '</option>';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param $users
     * @param $em
     */
    private function officeUserDeactive($users, $em)
    {
// all users are deactive
        /** @var User $users */
        foreach ($users as $user) {
            $user->setEnabled(false);
            $em->persist($user);
        }
    }

    /**
     * @param $users
     * @param $em
     */
    private function onlyAdminUsersActive($users, $em)
    {
//only admin users are active
        /** @var User $users */
        foreach ($users as $user) {

            if (strtoupper($user->getOffice()->getType()) == 'UDC') {
                $user->setEnabled(true);
                $em->persist($user);
            }

            if (in_array('ROLE_MANAGE_USERS', $user->getRoles())) {
                $user->setEnabled(true);
                $em->persist($user);
            }
        }
    }

    public function officeStatusAction(Request $request) {

        if (!$this->isCsrfTokenValid('office_move_action', $request->request->get('_token'))) {
            $this->addFlash('error', $this->get('translator')->trans('token incorrect'));
            return new Response();
        }
        $requestAll = $request->request->all();

        if (isset($requestAll['chk'])) {
            $ids = $request->request->get('chk');
            $status = $request->request->get('selectedChk');
            $type = $request->request->get('type');
           $this->officeStatusChanges($status,$ids);
            $this->addFlash('success', $this->get('translator')->trans('work done'));

            if(strtoupper($type) == 'UDC'){
                return $this->redirect($this->generateUrl('udc_office_list'));
            } elseif(strtoupper($type) == 'DC'){
                return $this->redirect($this->generateUrl('office_list'));
            }
        }
        $this->addFlash('error', $this->get('translator')->trans('nothing done'));

        return new Response();
    }

    public function officeStatusChanges($status,$ids) {

        $em = $this->getDoctrine()->getEntityManager();
        $manager = $this->getDoctrine()->getRepository('AppBundle:Office');
        $UserManager = $this->getDoctrine()->getRepository('RbsUserBundle:User');

        if (count($ids) < 1) {
            return;
        }

        foreach ($ids as $key=>$val) {

            $office =  $manager->find($key);
            $users  =  $UserManager->findByOffice($office);

            if($status){
                $this->onlyAdminUsersActive($users, $em);
                $office->setActive(true);
            }else {
                $this->officeUserDeactive($users, $em);
                $office->setActive(false);
            }
           // $em->persist($office);
        }
        $em->flush();
    }
}
