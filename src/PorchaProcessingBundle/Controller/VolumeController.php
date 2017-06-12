<?php

namespace PorchaProcessingBundle\Controller;

use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Form\VolumeIndexType;
use PorchaProcessingBundle\Form\VolumeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VolumeController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_CREATE, ROLE_VOLUME_BOOK_UPDATE")
     */
    public function updateVolumeAction(Request $request, Volume $volume = null)
    {
        if ($volume == null) {
            $volume = new Volume();
        }

        $form = $this->createForm(new VolumeType(
            $this->getUser(),
            $volume,
            array('ROLE_VOLUME_BOOK_APPROVED' => ($this->isGranted('ROLE_VOLUME_BOOK_APPROVED')))),
            $volume);

        $alreadyApproved = $volume->isApproved();
        $exVolumeMouzas = clone $volume->getVolumeMouzas();

        if ($request->isMethod('post')) {

            $form->handleRequest($request);

            if ($alreadyApproved && $form->get('approved')->getData()) {
                $this->addFlash('error', $this->get('translator')->trans('volume already approved. you are not allowed to change an approved volume'));
                return $this->redirect($this->generateUrl('volume_update', array('id'=> $volume->getId())));
            }

            if ($form->isValid()) {

                if (!$volume->getId()) {
                    $this->get('porcha_processing.service.volume_manager')->addVolume($volume);
                    $this->addFlash('success', $this->get('translator')->trans('Volume added'));
                } else {
                    $this->get('porcha_processing.service.volume_manager')->updateVolume($volume, $exVolumeMouzas);
                    if (!$alreadyApproved) {
                        $this->addFlash('success', $this->get('translator')->trans('Volume updated'));
                    }
                }

                return $this->redirect($this->get('session')->get('referer', $this->generateUrl('volume_list')));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = ($volume->getId()) ? $this->generateUrl('volume_update', array('id'=> $volume->getId())) : $this->generateUrl('volume_create');
        $data['volume'] = $volume;
        $data['alreadyApproved'] = $alreadyApproved;
        $data['hideSubmit'] = $alreadyApproved && !$this->isGranted('ROLE_VOLUME_BOOK_APPROVED') ? false : true;

        return $this->render('PorchaProcessingBundle:Volume:update_volume.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_VIEW")
     */
    public function volumeListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $volumes = $this->get('porcha_processing.service.volume_manager')->getVolumeList($request->query->all());
        $data['volumes']  = $this->get('knp_paginator')->paginate($volumes, $page, $perPage);

        $this->get('session')->set('referer', $request->getRequestUri());

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Volume:volume_list_sub.html.twig', $data);
        } else {
            $data['search_url'] = $this->generateUrl('volume_list');
            $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));
            $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
            return $this->render('PorchaProcessingBundle:Volume:volume_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_VIEW")
     */
    public function khatianListAction(Request $request, Volume $volume, $type)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.volume_manager')->getKhatianListByVolume($volume, $request->query->all());
        $khatianStatusNames = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatusNames();
        $khatianStatusCount = $this->get('porcha_processing.service.volume_manager')->getVolumeKhatianStatusCount($volume);

        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);
        $data['khatian_status_count']  = $khatianStatusCount;
        $data['khatian_status_names']  = $khatianStatusNames;
        $data['title'] = 'Khatian List';
        $data['volume'] = $volume;
        $data['volume_templetes'] = $this->get('porcha_processing.service.volume_manager')->getVolumeTemplates($volume);
        $data['type'] = $type;
        $data['info'] = $this->get('porcha_processing.service.volume_manager')->getVolumeDatails($volume);
        $data['survey_templates'] = $this->get('template.service.template_manager')->getKhatianEntryTemplates($this->getUser()->getOffice()->getId(), $volume->getSurvey()->getType());

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($request->isXmlHttpRequest(), $data);
    }

    private function renderKhatianList($isXmlHttpRequest = false, $data)
    {
        if ($isXmlHttpRequest) {
            return $this->render('PorchaProcessingBundle:Volume:khatian_list_sub.html.twig', $data);
        }
        return $this->render('PorchaProcessingBundle:Volume:khatian_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_DELETE")
     */
    public function deleteVolumeAction(Volume $volume) {

        if ($this->get('porcha_processing.service.volume_manager')->deleteVolume($volume)) {
            $this->addFlash('success', $this->get('translator')->trans('Volume deleted'));
        } else {
            $this->addFlash('error', $this->get('translator')->trans('Volume can not be deleted'));
        }
        return $this->redirect($this->generateUrl('volume_list'));
    }

    public function checkVolumeNoExistsAction(Request $request) {

        $ret = $this->get('porcha_processing.service.volume_manager')->checkVolumeNoExists($request->request->all());
        return new JsonResponse(array('status' => (!$ret), 'message' => ($ret) ? $this->get('translator')->trans('This volume No already exists') : ''));
    }

    public function checkKhatianRangeAction(Request $request) {

        $ret = $this->get('porcha_processing.service.volume_manager')->checkVolumeNoExists($request->request->all(), true);
        return new JsonResponse(array('status' => (!$ret), 'message' => ($ret) ? $this->get('translator')->trans('check the khatian range') : ''));
    }

    public function volumeNoVerifyOldAction(Request $request) {

        $msg = '';
        $ret = false;

        if (empty($request->request->all()['volumeId'])) {
            $ret = $this->get('porcha_processing.service.volume_manager')->ifVolumeNoExist($request->request->all());
        }

        if ($ret !== false) {
            $msg .= $ret . " মৌজাতে ভলিউম নং উপস্থিত । <br/>";
        }

        $ret = $this->get('porcha_processing.service.volume_manager')->ifKhatianRangeExist($request->request->all());

        if ($ret !== false) {
            $msg .=  $ret['mouza'] . " মৌজাতে " . $ret['startKhatianNo'] . " - " . $ret['endKhatianNo'] . " খতিয়ান ব্যাপ্তি   উপস্থিত । ";
        }

        return new JsonResponse(array('status' => (!empty($ret)) ? true : false, 'msg' => $msg));

    }

    public function volumeNoVerifyAction(Request $request) {

        $msg = '';
        $retVolume = false;
        $retRange = false;

        if (empty($request->request->all()['volumeId'])) {
            $retVolume = $this->get('porcha_processing.service.volume_manager')->ifVolumeNoExist($request->request->all());
        }

        if ($retVolume !== false) {
            $msg .= $retVolume . " মৌজাতে ভলিউম নং উপস্থিত । <br/>";
        }

        $retRange = $this->get('porcha_processing.service.volume_manager')->ifKhatianRangeExist($request->request->all());

        if ($retRange !== false) {
            $msg .=  $retRange['mouza'] . " মৌজাতে " . $retRange['startKhatianNo'] . " - " . $retRange['endKhatianNo'] . " খতিয়ান ব্যাপ্তি   উপস্থিত । ";
        }

        return new JsonResponse(array('status' => (!empty($msg)) ? true : false, 'msg' => $msg));

    }

    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_APPROVED")
     */
    public function approvedVolumeAction(Request $request, Volume $volume)
    {
        $class = $volume->isApproved() ? 'red' : 'green';
        $label = $volume->isApproved() ? 'Not Approved' : 'Approved';
        $form = $this->createFormBuilder(null, array('attr' => array('id' => 'approve-form')))
            ->setAction($this->generateUrl('volume_khatian_approved', array('id' => $volume->getId())))
            ->add('approved', 'submit', array('label' => $label, 'attr' => array('class' => 'btn ' . $class)))
            ->setMethod('POST')
            ->getForm();

        if ($request->isMethod('POST')) {

            $volume->setApproved(!$volume->isApproved());

            $em = $this->getDoctrine()->getManager();
            $em->persist($volume);
            $em->flush();

            $this->addFlash('success', $this->get('translator')->trans('Volume updated'));

            return $this->redirect($this->generateUrl('volume_list'));
        }

        return $this->render('@PorchaProcessing/Volume/approved_preview.html.twig', array(
            'volume' => $volume,
            'form' => $form->createView()
        ));
    }

    /**
     * @JMS\Secure(roles="ROLE_ARCHIVE_VOLUME")
     */
    public function archiveVolumeAction(Request $request, Volume $volume)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('archive_volume_action_' . $volume->getId(), $data['_token'])) {

            if ($this->get('porcha_processing.service.volume_manager')->archiveVolume($volume)) {
                $this->addFlash('success', $this->get('translator')->trans('Volume archived'));
                return new JsonResponse(array('status' => true));
            }
        }

        $this->addFlash('error', $this->get('translator')->trans('Volume not archived'));
        return new JsonResponse(array('status' => false));
    }

    /**
     * @JMS\Secure(roles="ROLE_VOLUME_BOOK_CREATE")
     */
    public function volumeIndexAction(Request $request, Volume $volume) {

        $form = $this->createForm(new VolumeIndexType($volume), $volume);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('porcha_processing.service.volume_manager')->saveVolumeIndexes($volume);
                $this->addFlash('success', $this->get('translator')->trans('Volume index saved'));
                return $this->redirect($this->generateUrl('volume_khatian_list', array('id' => $volume->getId())));
            }
        }

        $data['info'] = $this->get('porcha_processing.service.volume_manager')->getVolumeDatails($volume);
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('volume_index', array('id' => $volume->getId()));

        return $this->render('PorchaProcessingBundle:Volume:volume_index.html.twig', $data);

    }

    public function noEntryKhatiansAction(Volume $volume) {

        $mouzas = $this->get('porcha_processing.service.khatian_manager')->getNoEntryKhatiansByVolume($volume);
        return $this->render('PorchaProcessingBundle:Volume:no_entry_khatians.html.twig', array(
            'mouzas' => $mouzas
        ));
    }
}
