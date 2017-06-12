<?php

namespace PorchaProcessingBundle\Controller;

use AppBundle\Entity\User;
use PorchaProcessingBundle\Form\KhatianPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use PorchaProcessingBundle\Entity\Template;
use PorchaProcessingBundle\Form\TemplateType;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TemplateController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_CREATE_NEW_KHATIAN_TEMPLATE")
     */
    public function newAction(Request $request) {

        return $this->getTemplateForm(new Template());
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_NEW_KHATIAN_TEMPLATE")
     */
    public function editAction(Request $request, Template $template) {

        return $this->getTemplateForm($template);
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_NEW_KHATIAN_TEMPLATE")
     */
    public function createAction(Request $request) {

        $template = new Template();
        return $this->saveTemplateRequest($request, $template, $this->get('translator')->trans('New template created'));
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_NEW_KHATIAN_TEMPLATE")
     */
    public function updateAction(Request $request, Template $template) {

        if (!$this->isTemplateEditable($template)) {
            throw new HttpException('412', $this->get('translator')->trans('you are not allowed to update'));
        }
        return $this->saveTemplateRequest($request, $template, $this->get('translator')->trans('Template updated'));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_TEMPLATE_LIBRARY")
     */
    public function ministryTemplateListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $requestAll = $request->query->all();
        $district = (!empty($requestAll['ff']['d.geocode'])) ? $requestAll['ff']['d.geocode'] : null;

        $templates = $this->templateList($requestAll, $district);
        $data['templates']  = $this->get('knp_paginator')->paginate($templates, $page, 100);
        $data['search_url'] = $this->generateUrl('template_ministry_template_list');
        $data['this_user'] = $this->getUser()->getId();
        $data['can_edit_any'] = $this->canEditAnyTeamplate();

        return $this->renderTemplateList('MINISTRY', $request->isXmlHttpRequest(), $data, $district);
    }

    private function templateList($requestAll, $district) {

        if ($district) {
            return $this->get('template.service.template_manager')->getOfficeTemplateList($requestAll, $district);
        }
        return $this->get('template.service.template_manager')->getTemplateList($requestAll);
    }

    private function renderTemplateList($officeType = 'DC_OFFICE', $isXmlHttpRequest = false, $data, $district = false) {

        $data['office_template'] = $district;
        if ($isXmlHttpRequest) {
            return $this->render('PorchaProcessingBundle:Template:template_list_sub.html.twig', $data);
        }

        if ($officeType == 'DC_OFFICE') {
            $data['districts'] = array('' => $this->get('translator')->trans('All Templates'), '1' => $this->get('translator')->trans('My Office Templates'));
        } else {
            $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getDistrictsArray(array('approved' => 1));
        }

        return $this->render('PorchaProcessingBundle:Template:template_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_TEMPLATE_LIBRARY")
     */
    public function dcOfficeTemplateListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 100);

        $requestAll = $request->query->all();
        $userOffice = $this->get('session')->get('userOffice');
        $district = (!empty($requestAll['ff']['o.district'])) ? $userOffice['districtId'] : false;

        $templates = $this->templateList($requestAll, $district);
        $data['templates']  = $this->get('knp_paginator')->paginate($templates, $page, $perPage);
        $data['search_url'] = $this->generateUrl('template_dc_office_template_list');

        return $this->renderTemplateList('DC_OFFICE', $request->isXmlHttpRequest(), $data, $district);
    }


    public function previewTemplateAction(Request $request, Template $template) {

        $form = $this->createForm(new KhatianPageType());

        return $this->render('PorchaProcessingBundle:Template:preview_template.html.twig', array(
            'template' => $template,
            'form' => $form->createView(),
            'form_action' => '',
            'id' => 0
        ));
    }

    public function viewTemplateAction(Template $template) {

        $form = $this->createForm(new KhatianPageType());
        $officeTemplate = $this->get('template.service.template_manager')->getOfficeTemplate($template, $this->getUser());

        return $this->render('PorchaProcessingBundle:Template:view_template.html.twig', array(
            'template' => $template,
            'form' => $form->createView(),
            'form_action' => '',
            'id' => 0,
            'allow_set' => $this->isAllowedToCreateTemplate($this->getUser()),
            'is_set' => ($officeTemplate) ? true : false
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_SET_TEMPLATE_FOR_OFFICE")
     */
    public function setToMyOfficeAction(Template $template) {

        if (!$this->isAllowedToCreateTemplate($this->getUser())) {
            throw new HttpException(412, $this->get('translator')->trans('You are not allowed to set template'));
        }

        $ret = $this->get('template.service.template_manager')->setToMyOffice($template, $this->getUser());

        if ($ret !== false) {
            if ($ret == 'SET') {
                $this->addFlash("success", $this->get('translator')->trans("Template set for your Office"));
            } else {
                $this->addFlash("success", $this->get('translator')->trans("Template unset for your Office"));
            }
        } else {
            $this->addFlash("error", $this->get('translator')->trans("Template not set for your Office"));
        }

        return $this->redirect($this->generateUrl('template_view_template', array('id' => $template->getId())));
    }

    /**
     * @param $currentUser
     * @return bool
     */
    private function isAllowedToCreateTemplate($currentUser) {

        if (!$currentUser) {
            return false;
        }

        if(count(array_intersect(array('ROLE_MINISTRY_ADMIN', 'ROLE_MANAGE_USERS'), $currentUser->getRoles())) == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $template
     * @return Response
     */
    public function getTemplateForm($template)
    {
        $form = $this->createForm(new TemplateType($this->isGranted('ROLE_APPROVE_KHATIAN_TEMPLATE')), $template);
        return $this->render('PorchaProcessingBundle:Template:update_template.html.twig', $this->buildTemplateData($template, $form));
    }

    /**
     * @param Request $request
     * @param $template
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    private function saveTemplateRequest(Request $request,Template $template, $msg)
    {
        $form = $this->createForm(new TemplateType($this->isGranted('ROLE_APPROVE_KHATIAN_TEMPLATE')), $template);
        /** @var UploadedFile $file */
        $form->handleRequest($request);
        if ($form->isValid()) {
            $requestFile = $request->files->all();

            if ($this->isGranted('ROLE_APPROVE_KHATIAN_TEMPLATE')) {
                if ($template->isApproved()) {
                    $template->setApproved($template->isApproved());
                    $template->setApprovedAt(new \DateTime());
                    $template->setApprovedBy($this->getUser());
                }
            }

            if(isset($requestFile['doc_file'])){
                $file = $requestFile['doc_file'];
                $fileName= $template->getId().'-'.$file->getClientOriginalName();
                $file->move($template->getUploadDir(), $fileName);
                $template->setTemplateReference($fileName);
            }

            if (!$template->getId()) {
                $template->setCreatedAt(new \DateTime());
                $template->setCreatedBy($this->getUser());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();
            $this->addFlash("success", $msg);
            return $this->redirect($this->generateUrl('template_edit_template', array('id' => $template->getId())));
        }

        return $this->render('PorchaProcessingBundle:Template:update_template.html.twig', $this->buildTemplateData($template, $form));
    }

    /**
     * @param Template $template
     * @return string
     */
    private function createFormAction(Template $template)
    {
        return ($template->getId() == null ? $this->generateUrl('template_create_template') : $this->generateUrl('template_update_template', array('id' => $template->getId())));
    }

    /**
     * @param $template
     * @param $form
     * @return array
     */
    private function buildTemplateData($template, $form)
    {
        return array(
            'form' => $form->createView(),
            'form_action' => $this->createFormAction($template),
            'template' => $template,
            'surveys' => $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1)),
            'is_editable' => $this->isTemplateEditable($template)
        );
    }

    private function isTemplateEditable(Template $template) {

        if (!$this->canEditAnyTeamplate()) {

            if ($template && $template->isApproved()) {
                return false;
            }

            if ($template && $template->getCreatedBy() != $this->getUser()) {
                return false;
            }
        }
        return true;
    }

    private function canEditAnyTeamplate() {

        if ($this->isGranted('ROLE_EDIT_ANY_KHATIAN_TEMPLATE')) {
            return true;
        }
        return false;
    }
}