<?php

namespace PorchaProcessingBundle\Controller;

use AppBundle\Traits\EntityAssistant;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianCorrectionLog;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianPage;
use PorchaProcessingBundle\Entity\OfficeTemplate;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Form\KhatianCorrectionLogType;
use PorchaProcessingBundle\Form\KhatianPageType;
use PorchaProcessingBundle\Form\WorkflowActionForm;
use Proxies\__CG__\PorchaProcessingBundle\Entity\Mouza;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KhatianController extends Controller
{
    use EntityAssistant;
    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function newKhatianAction(Request $request, Volume $volume, $mouzaId, OfficeTemplate $officeTemplate, $type)
    {
        $mouza = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Mouza')->find($mouzaId);

        if (!$this->get('porcha_processing.service.workflow_manager')->isPageViewableByWorkflowTeams($volume, $mouza, $type)) {
            throw new HttpException(412, $this->get('translator')->trans("ENTRY_RESTRICTED"));
        }

        $khatianLog = $this->get('porcha_processing.service.khatian_manager')->newKhatian($volume, $mouza, $officeTemplate, $type);
        $khatianFirstPage = $this->get('porcha_processing.service.khatian_manager')->getKhatianFirstPageByKhatianLog($khatianLog);
        return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianFirstPage->getId())));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function entryOperatorKhatianPagesAction(Request $request, KhatianLog $khatianLog)
    {
        $serviceRequestId = $request->query->get('sr', 0);
        $khatianFirstPage = $this->get('porcha_processing.service.khatian_manager')->getKhatianFirstPageByKhatianLog($khatianLog);
        return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianFirstPage->getId())).'?sr='.$serviceRequestId);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function entryOperatorActionAction(Request $request, KhatianLog $khatianLog)
    {
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        if ($request->isMethod('post')) {

            $workflowAction = $request->request->get('workflowAction', 'DRAFT');

            $this->get('porcha_processing.service.khatian_manager')->updateKhatian($khatian, $workflowAction, $request->request->all());

            if ($workflowAction == 'DRAFT') {
                $this->addFlash('success', $this->get('translator')->trans('Khatian Saved'));
            } else {
                $this->addFlash('success', $this->get('translator')->trans('Khatian sent for verification'));
            }

            $this->getDoctrine()->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->updateTodayRecord();

            return $this->redirect($this->generateUrl('volume_khatian_list', array('id' => $khatian->getVolume()->getId())));
        }

        $data['form_action'] = $this->generateUrl('entry_operator_khatian_pages', array('id' => $khatianLog->getId()));
        $data['khatian_pages'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianPages($khatian->getLastVersion());
        $data['workflow'] = false;

        return $this->render('PorchaProcessingBundle:Khatian:entry_operator_khatian_pages.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function deleteKhatianPageAction(Request $request, KhatianPage $khatianPage, KhatianLog $khatianLog)
    {
        $this->isKhatianUpdatableByKhatianLog($khatianLog);

        $serviceRequestId = $request->query->get('sr', 0);

        if ($khatianLog->getEntryOperator()->getId() != $this->getUser()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        if ($this->get('porcha_processing.service.khatian_manager')->deleteKhatianPage($khatianPage)) {
            $this->addFlash('success',$this->get('translator')->trans('Page deleted'));
        } else {
            $this->addFlash('error',$this->get('translator')->trans('Page not deleted'));
        }
        return $this->redirect($this->generateUrl('entry_operator_khatian_pages', array('id' => $khatianLog->getId())).'?sr='.$serviceRequestId);
    }



    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function deleteKhatianAction(Request $request, Khatian $khatian)
    {
        $this->isKhatianUpdatableByKhatian($khatian);

        if ($khatian->getLastVersion()->getEntryOperator()->getId() != $this->getUser()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        if ($this->get('porcha_processing.service.khatian_manager')->deleteKhatian($khatian)) {
            $this->addFlash('success',$this->get('translator')->trans('Khatian deleted'));
        } else {
            $this->addFlash('error',$this->get('translator')->trans('Khatian not deleted'));
        }
        return $this->redirect($this->generateUrl('khatian_draft_khatians'));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_VIEW")
     */
    public function khatianPagePreviewBeforeEntryAction(Request $request, KhatianPage $khatianPage)
    {
        $khatian = $khatianPage->getKhatianVersion()->getKhatian();

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');

        if ($request->isMethod('post')) {

            $khatianData = $khatianManager->attachKhatianPageData($khatianPage, $khatian, $request->request->all());
            $khatianPages = $khatianManager->getKhatianPagePrintView(array($khatianData['khatianPage']), $khatianData['khatian']);

            return $this->render('@PorchaProcessing/Khatian/readonly_view_khatian_page.html.twig', array(
                'khatianPages' => $khatianPages,
                'khatian' => $khatian
            ));
        }

        return new Response('Nothing to preview');
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function entryOperatorKhatianPageAction(Request $request, KhatianLog $khatianLog, KhatianPage $khatianPage)
    {
        $this->isKhatianUpdatableByKhatianLog($khatianLog);

        if (!$khatianPage) {
            $khatianPage = $this->get('porcha_processing.service.khatian_manager')->getKhatianFirstPageByKhatianLog($khatianLog);
        }

        if ($khatianLog->getEntryOperator()->getId() != $this->getUser()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        $khatian = $khatianPage->getKhatianVersion()->getKhatian();
        $khatianPage->prependNewLine = true;

        $serviceRequestId = $request->query->get('sr', 0);
        $khatianNo = '';
        if ($request->query->get('khatian_no')) {
            $khatianNo = $request->query->get('khatian_no', '');
            $khatian->setKhatianNo($khatianNo);
        }

        $form = $this->createForm(new KhatianPageType($khatian, $khatianPage->getType(), $khatianLog), $khatianPage);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            $khatianAction = $request->request->get('khatianAction');
            $templateId = $request->request->get('page-template');

            $validationError = array('status' => true, 'message' => '');

            if (strtoupper($khatianPage->getType()) == 'PAGE1') {
                $validationError = $this->get('porcha_processing.service.khatian_manager')->khatianEntryValidate($request->request->all(), $khatianAction, $khatianLog->getId(), $khatian->getKhatianNo());

                if (!$validationError['status']) {
                    $form->addError(new FormError($validationError['message']));
                }
            }

            if ($form->isValid() || $validationError['status']) {

                $this->get('porcha_processing.service.khatian_manager')->updateKhatianPage($khatianPage, $request->request->all());

                $nextEntry = $request->request->get('nextEntry');

                return $this->khatianEntryAction($khatian, $khatianPage, $khatianAction, $nextEntry, $request, $khatianLog, $templateId, $serviceRequestId);
            }
        }

        $data['khatian'] = $khatian;
        $data['khatianLog'] = $khatianLog;
        $data['khatianPage'] = $khatianPage;
        $data['page_links'] = $this->get('porcha_processing.service.khatian_manager')->singleKhatianPages($khatianLog);
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianPage->getId())).'?sr='.$serviceRequestId;
        $data['delete_url'] = $this->generateUrl('delete_khatian_page', array('id' => $khatianPage->getId(), 'khatianLog' => $khatianLog->getId())).'?sr='.$serviceRequestId;
        $data['template'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianPageTemplate($khatianPage);
        $data['nd_messages'] = $this->get('porcha_processing.service.khatian_manager')->getAllNonDeliverableMessages();
        $data['survey_templates'] = $this->get('template.service.template_manager')->getKhatianEntryTemplates($this->getUser()->getOffice()->getId(), $khatian->getVolume()->getSurvey()->getType());
        $data['serviceRequestId'] = $serviceRequestId;

        return $this->render('PorchaProcessingBundle:Khatian:update_khatian.html.twig', $data);
    }

    private function khatianEntryAction(Khatian $khatian, $khatianPage, $khatianAction, $nextEntry, $request, $khatianLog, $templateId, $serviceRequestId) {

        switch (strtoupper($khatianAction)) {

            case 'PAGE_SAVE':
                $this->addFlash('success',$this->get('translator')->trans('Khatian page saved'));
                return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianPage->getId())).'?sr='.$serviceRequestId);
            case 'PAGE1_ADDITIONAL':
            case 'PAGE2':
            case 'PAGE2_ADDITIONAL':
                $ret = $this->get('porcha_processing.service.khatian_manager')->createKhatianPages($khatian, $khatianAction, $templateId);
                if ($ret['status']) {
                    $this->addFlash('success',$this->get('translator')->trans('Khatian page created'));
                } else {
                    $this->addFlash('error',$this->get('translator')->trans('Khatian page already created'));
                }
                return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $ret['khatianPage']->getId())).'?sr='.$serviceRequestId);

            case 'DRAFT':
            case 'READY_FOR_VERIFICATION':

                $this->get('porcha_processing.service.khatian_manager')->srUpdateKhatian($khatianLog, $khatianAction, $request->request->all(), $serviceRequestId);

                if ($khatianAction == 'DRAFT') {
                    $this->addFlash('success', $this->get('translator')->trans('Khatian Saved'));
                } else {
                    $this->addFlash('success',$this->get('translator')->trans('Khatian sent for verification'));
                }
                if ($nextEntry) {
                    $khatianPage = $this->get('porcha_processing.service.khatian_manager')->getKhatianFirstPageByKhatianLog($khatianLog);
                    return $this->redirect($this->generateUrl('khatian_new_khatian', array(
                            'volume' => $khatian->getVolume()->getId(),
                            'mouzaId' => $khatian->getMouza()->getId(),
                            'officeTemplate' => $khatianPage->getOfficeTemplate()->getId(),
                            'type' => (!$khatianLog->isBatch()) ? 'SERVICE' : 'BATCH'
                        ))
                    );
                }
                return $this->redirect($this->get('session')->get('referer', $this->generateUrl('dashboard')));
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function changeEntryTemplateAction(Request $request, KhatianLog $khatianLog, KhatianPage $khatianPage, OfficeTemplate $officeTemplate) {

        $this->isKhatianUpdatableByKhatianLog($khatianLog);

        $serviceRequestId = $request->query->get('sr', 0);

        if ($this->get('porcha_processing.service.khatian_manager')->changeEntryTemplate($khatianPage, $officeTemplate)) {
            $this->addFlash('success', $this->get('translator')->trans('Template changed'));
        } else {
            $this->addFlash('danger', $this->get('translator')->trans('Template not changed'));
        }
        return $this->redirect($this->generateUrl('entry_operator_khatian_page', array('id' => $khatianLog->getId(), 'khatianPage' => $khatianPage->getId())).'?sr='.$serviceRequestId);

    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function workflowKhatianPagesAction(Request $request, KhatianLog $khatianLog)
    {
        $this->isKhatianUpdatableByKhatianLog($khatianLog);

//        if (!$this->get('porcha_processing.service.workflow_manager')->isWorkflowPageViewableByKhatianLog($khatianLog)) {
//            throw $this->createAccessDeniedException($this->get('translator')->trans('ENTRY_RESTRICTED'));
//        }

        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatianManager->lock($khatianLog, $this->getUser());

        $khatianPageEntities = $khatianManager->getKhatianPages($khatian->getLastVersion());
        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);

        $workflowForm = $this->createForm(new WorkflowActionForm(true),
            array('correctionMessages' => $this->get('porcha_processing.service.khatian_correction_log_manager')->findAllByKhatianPages($khatianPageEntities))
        );

        return $this->render(
            '@PorchaProcessing/Khatian/workflow_view_khatian.html.twig', array(
                'action_url'   => $this->generateUrl('khatian_workflow_action', array('id' => $khatian->getId())),
                'workflowForm' => $workflowForm->createView(),
                'khatianPages' => $khatianPages,
                'khatian'      => $khatian,
                'khatianLog'      => $khatianLog,
                'pagination'   => $pagination,
                'non_deliverables' => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            )
        );
    }


    public function khatianPagesAction(KhatianLog $khatianLog)
    {
        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        if ($khatian->isDisplayRestricted()) {
            throw new HttpException(412, $this->get('translator')->trans("You dont have permission to view this Khatian"));
        }

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');

        $khatianPageEntities = $khatianManager->getKhatianPages($khatianLog->getKhatianVersion());
        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);

        return $this->render('@PorchaProcessing/Khatian/readonly_view_khatian.html.twig', array(
            'khatianPages' => $khatianPages,
            'pagination' => $pagination,
            'khatian' => $khatian,
            'khatianLog' => $khatianLog,
            'non_deliverables' => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            'non_deliverable_template' => $this->renderView('@PorchaProcessing/Khatian/nondeliverable.html.twig', array('survey_name' => $khatian->getVolume()->getSurvey()->getName()))
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function workflowActionAction(Request $request, KhatianLog $khatianLog)
    {
        $this->isKhatianUpdatableByKhatianLog($khatianLog);

        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        $workflowAction = $request->request->get('porcha_processing_workflow_action[workflowAction]', 'ACTION_BACK', true);
        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatianPageEntities = $khatianManager->getKhatianPages($khatian->getLastVersion());

        $correctionLogs = $this->get('porcha_processing.service.khatian_correction_log_manager')->findAllByKhatianPages(
            $khatianPageEntities
        );
        $workflowForm = $this->createForm(new WorkflowActionForm(true),
            array('correctionMessages' => $correctionLogs)
        );
        $workflowForm->handleRequest($request);

        if ($workflowForm->isValid()) {

            $this->get('porcha_processing.service.workflow_manager')->srWorkflowAction($khatianLog, $workflowAction, $correctionLogs, $this->getUser());
            if ($workflowAction == 'ACTION_FORWARD') {

                $label = $this->get('translator')->trans('Khatian Forwarded');
                if ($khatian->isArchived()) {
                    $label = ($khatianLog->isBatch()) ?  $this->get('translator')->trans('Khatian Archived') : $this->get('translator')->trans('Khatian sent for delivery');
                }
                $this->addFlash('success', $this->get('translator')->trans($label));
            } else {
                $this->addFlash('success',$this->get('translator')->trans('Khatian sent back to entry operator'));
            }

            return $this->redirect($this->get('session')->get('referer', 'khatian_re_assigned_khatian_list'));
        }

        return $this->redirect($this->get('session')->get('referer', $this->generateUrl('khatian_re_assigned_khatian_list')));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function draftsAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getDraftKhatians($request->query->all());
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('translator')->trans('Draft Khatians');
        $data['tab'] = 'DRAFT_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_draft_khatians');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    public function moveKhatiansToNextStepAction(Request $request) {

        if (!$this->isCsrfTokenValid('khatian_move_action', $request->request->get('_token'))) {
            $this->addFlash('error', $this->get('translator')->trans('token incorrect'));
            return new Response();
        }

        $requestAll = $request->request->all();

        if (isset($requestAll['chk'])) {
            $this->get('porcha_processing.service.workflow_manager')->moveKhatiansToNextStep($request->request->get('selectedChk'), $request->request->get('chk'));
            $this->addFlash('success', $this->get('translator')->trans('work done'));
            return new Response();
        }
        $this->addFlash('error', $this->get('translator')->trans('nothing done'));

        return new Response();
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function sentKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getSentKhatianList($request->query->all());
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('translator')->trans('Sent Khatians');
        $data['tab'] = 'SENT_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_sent_khatian_list');
        $data['hide_checkbox'] = true;

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function reAssignedKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getReAssignedKhatianList($request->query->all());
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('porcha_processing.service.workflow_manager')->khatianListTitle($this->getUser());
        $data['tab'] = 'RE_ASSIGNED_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_re_assigned_khatian_list');
        $data['current_user'] = $this->getUser();

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_VERIFICATION")
     */
    public function verifyNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getNewKhatianListByStatus($request->query->all(), 'READY_FOR_VERIFICATION');
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Verification)');
        $data['tab'] = 'VERIFY_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_verify_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_COMPARISON")
     */
    public function compareNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getNewKhatianListByStatus($request->query->all(), 'READY_FOR_COMPARISON');
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Compararison');
        $data['tab'] = 'COMPARE_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_compare_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_APPROVAL")
     */
    public function approveNewKhatianListAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.workflow_manager')->getNewKhatianListByStatus($request->query->all(), 'READY_FOR_APPROVAL');
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['title'] = $this->get('translator')->trans('Batched') . ' ' . $this->get('translator')->trans('Khatian List (Ready for Approval)');
        $data['tab'] = 'APPROVE_NEW_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_approve_new_khatian_list');

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_APPROVAL")
     */
    public function approvedKhatianListAction(Request $request)
    {
        if ($request->isMethod('POST') && $khatianIds = $request->request->get('khatians')) {
            if ($this->get('porcha_processing.service.workflow_manager')->archiveKhatian($khatianIds, $this->getUser())) {
                $this->addFlash('success', 'Selected Khatians Archived');
            } else {
                $this->addFlash('error', 'Invalid Operation');
            }

            return $this->redirect($this->generateUrl('khatian_approved_khatian_list'));
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatians = $this->get('porcha_processing.service.workflow_manager')->getNewKhatianListByStatus($request->query->all(), 'APPROVED');
        $data['khatians']  = $this->get('knp_paginator')->paginate($khatians, $page, $perPage);
        $data['title'] = 'Approved Khatians';
        $data['tab'] = 'APPROVED_KHATIANS';
        $data['search_url'] = $this->generateUrl('khatian_approved_khatian_list');
        $data['current_user'] = $this->getUser();

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($data);
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY")
     */
    public function khatianMovetoDraftAction(KhatianLog $khatianLog)
    {
        if ($khatianLog->getEntryOperator()->getId() != $this->getUser()->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("You don't have permission to move this Khatian"));
        }

        if ($khatianLog->getKhatianStatus() == 'READY_FOR_VERIFICATION' && !$khatianLog->isLocked()) {
            $khatianLog->setKhatianStatus('DRAFT');
            $this->get('porcha_processing.service.khatian_manager')->update($khatianLog);
            $this->addFlash('success',$this->get('translator')->trans('Khatian has moved to draft'));

            $this->getDoctrine()->getRepository('PorchaProcessingBundle:Report\EntryStatistics')->updateTodayRecord();

            return $this->redirect($this->generateUrl('khatian_sent_khatian_list'));
        } else {
            $this->addFlash('error',$this->get('translator')->trans('Khatian can not move now'));

            return $this->redirect($this->generateUrl('khatian_sent_khatian_list'));
        }
    }

    private function renderKhatianList($data)
    {
        $data['menu'] = $this->get('porcha_processing.service.khatian_action_menu_manager');

        if ($this->get('request')->isXmlHttpRequest()) {
            if ($data['tab'] == 'APPROVED_KHATIANS') {
                return $this->render('PorchaProcessingBundle:Khatian:khatian_approved_list_sub.html.twig', $data);
            } else {
                return $this->render('PorchaProcessingBundle:Khatian:khatian_list_sub.html.twig', $data);
            }
        }

        $data['surveys'] = $this->get('porcha_processing.service.mouza_option_manager')->getSurveys();
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();

        return $this->render('PorchaProcessingBundle:Khatian:khatian_list.html.twig', $data);
    }

    public function checkKhatianNoExistsAction(Request $request) {

        $ret = $this->get('porcha_processing.service.khatian_manager')->checkKhatianNoExists(
            $request->request->get('khatianLogId'),
            $request->request->get('khatianNo'),
            $request->request->get('except')
        );
        return new JsonResponse(array('status' => ($ret['status']), 'ret' => $ret, 'message' => (!$ret['status']) ? $ret['message'] : ''));
    }

    public function batchKhatianSearchAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        if ($this->get('request')->isXmlHttpRequest()) {

            $khatianLogs = $this->get('porcha_processing.service.khatian_manager')->batchKhatianSearch($request->query->all());
            $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
            return $this->render('PorchaProcessingBundle:Khatian:khatian_search_sub.html.twig', $data);
        }

        $data['search_url'] = $this->generateUrl('batch_khatian_search');
        $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();

        return $this->render('PorchaProcessingBundle:Khatian:khatian_search.html.twig', $data);
    }


    public function updateTemplateFieldAction(Request $request) {

        $volumes = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Volume')->findAll();

        foreach ($volumes as $volume) {

            $volumeTemplate = $this->getDoctrine()->getRepository('PorchaProcessingBundle:VolumeTemplate')->findOneBy(array('volume' => $volume));

            if ($volumeTemplate) {

                $otPage1 = null;
                if ($volumeTemplate->getTemplatePage1()) {
                    $otPage1 = $this->getDoctrine()->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array('office' => $volume->getOffice(), 'template' => $volumeTemplate->getTemplatePage1()));
                }
                $otPage1Ad = null;
                if ($volumeTemplate->getTemplatePage1Additional()) {
                    $otPage1Ad = $this->getDoctrine()->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array('office' => $volume->getOffice(), 'template' => $volumeTemplate->getTemplatePage1Additional()));
                }
                $otPage2 = null;
                if ($volumeTemplate->getTemplatePage2()) {
                    $otPage2 = $this->getDoctrine()->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array('office' => $volume->getOffice(), 'template' => $volumeTemplate->getTemplatePage2()));
                }
                $otPage2Ad = null;
                if ($volumeTemplate->getTemplatePage2Additional()) {
                    $otPage2Ad = $this->getDoctrine()->getRepository('PorchaProcessingBundle:OfficeTemplate')->findOneBy(array('office' => $volume->getOffice(), 'template' => $volumeTemplate->getTemplatePage2Additional()));
                }

                $qb = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:KhatianPage')->createQueryBuilder('kp');
                $qb->join('kp.khatianVersion', 'kv');
                $qb->join('kv.khatian', 'k');
                $qb->join('k.volume', 'v');
                $qb->where('v.id = :volume')->setParameter('volume', $volume);
                $rows = $qb->getQuery()->getResult();

                $updateOfficeTemplate = null;
                foreach ($rows as $row) {

                    switch (strtoupper($row->getType())) {

                        case 'PAGE1':
                            $updateOfficeTemplate = $otPage1;
                            break;
                        case 'PAGE1_ADDITIONAL':
                            $updateOfficeTemplate = $otPage1Ad;
                            break;
                        case 'PAGE2':
                            $updateOfficeTemplate = $otPage2;
                            break;
                        case 'PAGE2_ADDITIONAL':
                            $updateOfficeTemplate = $otPage2Ad;
                            break;
                    }
                    $row->setOfficeTemplate($updateOfficeTemplate);
                    $this->getDoctrine()->getManager()->persist($row);

                }
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return new Response('done');

    }
    public function vrrSearchKhatianAction(Request $request){

        if (!$request->headers->has('X-ApiKey')) {
            return new Response('Required API key is missing.', 400);
        }

        if ($request->headers->get('X-ApiKey') != '123') {
            return new Response('Unauthorized access.', 401);
        }
        $data = $request->request->all();
        /* file_put_contents(
             $this->get('kernel')->getRootDir() . '/../request_data.txt', implode(", ", $data)
         );*/
        $repositoryServiceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Khatian');
        $khatians  = $repositoryServiceRequest->getKhatian($data);
        $ret = array();
        foreach ($khatians as $khatian) {
            $ret[] = array(
                'id' => $khatian->getId(),
                'upozila' => $khatian->getVolume()->getUpozila()->getName(),
                'mouza' => $khatian->getMouza()->getName(),
                'survey' => $khatian->getVolume()->getSurvey()->getName(),
                'jlNumber' => $khatian->getJlNumber()->getName(),
                'khatianNo' => $khatian->getKhatianNo(),
            );
        }
        $response = new JsonResponse($ret, 201, [
            'Content-type' => 'application/json'
        ]);
        return $response;
    }

    private function singleKhatian(KhatianLog $khatianLog) {

        $khatian = $khatianLog->getKhatianVersion()->getKhatian();

        if ($khatian->isDisplayRestricted()) {
            throw new HttpException(412, $this->get('translator')->trans("You dont have permission to view this Khatian"));
        }

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');

        $khatianPageEntities = $khatianManager->getKhatianPages($khatianLog->getKhatianVersion());
        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);

        return $this->renderView('@PorchaProcessing/Khatian/khatian_print_view.html.twig', array(
            'khatianPages' => $khatianPages,
            'pagination' => $pagination,
            'khatian' => $khatian,
            'khatianLog' => $khatianLog,
            'non_deliverables' => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            'non_deliverable_template' => $this->renderView('@PorchaProcessing/Khatian/nondeliverable.html.twig', array('survey_name' => $khatian->getVolume()->getSurvey()->getName()))
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_KHATIAN_ENTRY,ROLE_KHATIAN_VERIFICATION,ROLE_KHATIAN_COMPARISON,ROLE_KHATIAN_APPROVAL")
     */
    public function selectedKhatinsPrintAction(Request $request) {

        if (!$this->isCsrfTokenValid('khatian_move_action', $request->request->get('_token'))) {
            return new Response('Form token incorrect');
        }

        $requestAll = $request->request->all();

        if (isset($requestAll['chk'])) {

            if (count($requestAll['chk']) < 1) {
                return new Response($this->get('translator')->trans('Not Found'));
            }

            $html = "";
            foreach ($requestAll['chk'] as $key=>$val) {
                $khatianLog = $this->getDoctrine()->getEntityManager()->getRepository('PorchaProcessingBundle:KhatianLog')->find($key);
                $html .= $this->singleKhatian($khatianLog);
                $html .= '<div class="print-page"></div> ';
            }
            return $this->render('PorchaProcessingBundle:Khatian:multiple_khatian_print_view.html.twig', array(
                'page_content' => $html
            ));
        }

        return new Response();
    }

    public function updateCanonicalKhatianNosAction() {

        $this->get('porcha_processing.service.khatian_manager')->updateCanonicalKhatianNos();
        return new Response('done');
    }

    public function updateCanonicalVolumeNosAction() {

        var_dump($this->get('porcha_processing.service.khatian_manager')->updateCanonicalVolumeNos());
        return new Response('done');
    }

    private function isKhatianUpdatableByKhatianLog(KhatianLog $khatianLog) {

        if ($khatianLog->getKhatianVersion()->getKhatian()->isArchived() && !$khatianLog->getKhatianVersion()->getKhatian()->isReCorrection()) {
            throw new HttpException(412, $this->get('translator')->trans("you can not update an archived khatian"));
        }
        return true;
    }

    private function isKhatianUpdatableByKhatian(Khatian $khatian) {

        if ($khatian->isArchived() && !$khatian->isReCorrection()) {
            throw new HttpException(412, $this->get('translator')->trans("you can not update an archived khatian"));
        }
        return true;
    }

}
