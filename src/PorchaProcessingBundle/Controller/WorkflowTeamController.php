<?php

namespace PorchaProcessingBundle\Controller;

use PorchaProcessingBundle\Entity\WorkflowTeam;
use PorchaProcessingBundle\Form\WorkflowTeamType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;

class WorkflowTeamController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_VIEW_WORKFLOW_TEAM")
     */
    public function teamListAction(Request $request, $type)
    {
        return $this->render('PorchaProcessingBundle:WorkflowTeam:index.html.twig', array(
            'teams' => $this->get('porcha_processing.service.workflow_manager')->getWorkflowTeams($type),
            'type' => $type
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_WORKFLOW_TEAM")
     */
    public function newAction(Request $request, $type)
    {
        $workflowTeam = new WorkflowTeam();
        $form = $this->teamForm($workflowTeam);

        return $this->renderCreateTeamplate($form, $type);
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_WORKFLOW_TEAM")
     */
    public function createTeamAction(Request $request, $type) {

        $workflowTeam = new WorkflowTeam();
        $form = $this->teamForm($workflowTeam);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('porcha_processing.service.workflow_manager')->createWorkflowTeam($workflowTeam, $type);
                $this->addFlash('success', $this->get('translator')->trans('group').' '.$this->get('translator')->trans('created'));
                return $this->redirect($this->generateUrl('workflow_team_list', array('type' => $type)));
            }
        }

        return $this->renderCreateTeamplate($form, $type);
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_WORKFLOW_TEAM")
     */
    public function editTeamAction(Request $request, WorkflowTeam $workflowTeam, $type)
    {
        $form = $this->teamForm($workflowTeam);
        return $this->renderUpdateTeamplate($form, $type, $workflowTeam);
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_WORKFLOW_TEAM")
     */
    public function updateTeamAction(Request $request, WorkflowTeam $workflowTeam = null, $type) {

        $form = $this->teamForm($workflowTeam);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('porcha_processing.service.workflow_manager')->updateWorkflowTeam($workflowTeam);
                $this->addFlash('success', $this->get('translator')->trans('group').' '.$this->get('translator')->trans('updated'));
                return $this->redirect($this->generateUrl('workflow_team_list', array('type' => $type)));
            }
        }

        return $this->renderUpdateTeamplate($form, $type, $workflowTeam);
    }

    private function renderCreateTeamplate($form, $type) {

        return $this->render('PorchaProcessingBundle:WorkflowTeam:save.html.twig', array(
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('workflow_team_create', array('type' => $type)),
            'type' => $type,
            'workflow_team' => null
        ));
    }

    private function renderUpdateTeamplate($form, $type, $workflowTeam) {

        return $this->render('PorchaProcessingBundle:WorkflowTeam:save.html.twig', array(
            'form' => $form->createView(),
            'form_action' => $this->generateUrl('workflow_team_update', array('id' => $workflowTeam->getId(), 'type' => $type)),
            'type' => $type,
            'workflow_team' => $workflowTeam
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_CREATE_WORKFLOW_TEAM")
     */
    public function deleteTeamAction(Request $request, WorkflowTeam $workflowTeam, $type) {

        $data = $request->request->all();

        if ($this->isCsrfTokenValid('delete_team_action_' . $workflowTeam->getId(), $data['_token'])) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($workflowTeam);
            $em->flush();

            return $this->redirect($this->generateUrl('workflow_team_list', array('type' => $type)));
        }
    }

    private function teamForm(WorkflowTeam $workflowTeam) {

        $relatedDistrictIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();
        return $this->createForm(new WorkflowTeamType($this->getUser()->getOffice(), $relatedDistrictIds), $workflowTeam);
    }
}
