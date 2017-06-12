<?php

namespace PorchaProcessingBundle\Controller;


use AppBundle\Entity\Document;
use PorchaProcessingBundle\Entity\District;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Thana;
use PorchaProcessingBundle\Entity\Upozila;
use PorchaProcessingBundle\Form\DistrictType;
use PorchaProcessingBundle\Form\MouzaType;
use PorchaProcessingBundle\Form\ThanaType;
use PorchaProcessingBundle\Form\UpozilaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\File;
use JMS\SecurityExtraBundle\Annotation as JMS;


class MouzaOptionController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_MOUZA_OPTION_MANAGEMENT")
     */
    public function updateMouzaAction(Request $request, Mouza $mouza = null)
    {
        $mode = "add";
        $jlnumbers = null;
        if ($mouza) {
            $mode = "edit";
            $jlnumbers = $this->get('porcha_processing.service.mouza_option_manager')->getMouzaJlnumberInfo($mouza);
        }

        $relatedDistrictIds = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistrictIds();
        $form = $this->createForm(new MouzaType($jlnumbers,
            $relatedDistrictIds,
            $this->isGranted('ROLE_GEOGRAPHICAL_INFO_APPROVED'),
            (!$this->isGranted('ROLE_GEOGRAPHICAL_INFO_APPROVED') && $mouza && $mouza->isApproved())
            ), $mouza);

        if ($request->isMethod('post')) {

            if ($mouza) {
                if ($mouza->isApproved() && isset($request->request->all()['porcha_processing_mouza']['approved'])) {
                    $this->addFlash('error',$this->get('translator')->trans('mouza already approved. you are not allowed to change an approved mouza'));
                    return $this->redirect($this->generateUrl('mouza_update', array('id' => $mouza->getId())));
                }
            }

            $form->handleRequest($request);

            if ($form->isValid()) {

                if ($mode == 'edit') {
                    $this->get('porcha_processing.service.mouza_option_manager')->updateMouza($mouza, $request->request->all());
                    $this->addFlash('success',$this->get('translator')->trans('Mouza updated'));
                } else {
                    $this->get('porcha_processing.service.mouza_option_manager')->createMouza($request->request->all());
                    $this->addFlash('success',$this->get('translator')->trans('Mouza added'));
                }

                return $this->redirect($this->generateUrl('mouza_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['mode'] = $mode;
        if ($mode == "edit") {
            $data['title'] = 'Edit Mouza';
        }

        $data['importForm'] = $this->createImportForm(new Document())->createView();
        return $this->render('PorchaProcessingBundle:MouzaOption:update_mouza.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_OPTION_MANAGEMENT")
     */
    public function deleteMouzaAction(Mouza $mouza)
    {
        $mouza->setDeleted(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($mouza);
        $em->flush();
        $this->addFlash('success',$this->get('translator')->trans('Mouza Deleted'));

        return $this->redirect($this->generateUrl('mouza_list'));
    }

    public function mouzaListAction(Request $request) {

        $document = new Document();
        $importForm = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $importForm->handleRequest($request);

            if ($importForm->isValid()) {
                $this->get('app.model.data_import_manager')->importMouzas($document);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));

                return $this->redirect($this->generateUrl('mouza_list'));
            }
        }

        $data['filterBy'] = 'UPOZILA';
        $data['tab'] = 'UPOZILA';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 100);

        $mouzas = $this->get('porcha_processing.service.mouza_option_manager')->getMouzaList($request->query->all(), $data['filterBy']);
        $data['mouzas'] = $this->get('knp_paginator')->paginate($mouzas, $page, $perPage);
        $data['record_count'] = count($mouzas);
        $data['upozilas_search'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedUpozilas();

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:MouzaOption:mouza_list_sub.html.twig', $data);
        } else {
            $data['importForm'] = $importForm->createView();
            $data['form_action'] = $this->generateUrl('mouza_list');
            $data['search_url'] = $this->generateUrl('mouza_list');
            return $this->render('PorchaProcessingBundle:MouzaOption:mouza_list.html.twig', $data);
        }
    }

    public function mouzaListThanaAction(Request $request) {

        $data['filterBy'] = 'THANA';
        $data['tab'] = 'THANA';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 100);

        $mouzas = $this->get('porcha_processing.service.mouza_option_manager')->getMouzaList($request->query->all(), $data['filterBy']);
        $data['mouzas']  = $this->get('knp_paginator')->paginate($mouzas, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:MouzaOption:mouza_list_sub.html.twig', $data);
        } else {
            $data['search_url'] = $this->generateUrl('mouza_list_thana');
            return $this->render('PorchaProcessingBundle:MouzaOption:mouza_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_OPTION_MANAGEMENT")
     */
    public function updateDistrictAction(Request $request, District $district_ = null)
    {
        $mode = "add";
        $district = new District();
        if ($district_) {
            $district = $district_;
            $mode = "edit";
        }

        $form = $this->createForm(new DistrictType(), $district);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $formData = $request->request->all();
                $this->get('porcha_processing.service.mouza_option_manager')->updateDistrict($formData, $district);

                $this->addFlash('success',$this->get('translator')->trans('District added'));
                return $this->redirect($this->generateUrl('district_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('district_create');
        $data['mode'] = $mode;
        if ($mode == "edit") {
            $data['form_action'] = $this->generateUrl('district_update', array('id' => $district->getId()));
            $data['title'] = 'Edit District';
        }

        return $this->render('PorchaProcessingBundle:MouzaOption:update_district.html.twig', $data);
    }

    public function districtListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importDistricts($document);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('district_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $upozilas = $this->get('porcha_processing.service.mouza_option_manager')->getDistrictList($request->query->all());
        $data['districts']  = $this->get('knp_paginator')->paginate($upozilas, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:MouzaOption:district_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('district_list');
            return $this->render('PorchaProcessingBundle:MouzaOption:district_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_OPTION_MANAGEMENT")
     */
    public function updateUpozilaAction(Request $request, Upozila $upozila_ = null)
    {
        $mode = "add";
        $upozila = new Upozila();
        if ($upozila_) {
            $upozila = $upozila_;
            $mode = "edit";
        }

        $form = $this->createForm(new UpozilaType($this->getUser(), $this->get('security.authorization_checker')->isGranted('ROLE_GEOGRAPHICAL_INFO_APPROVED')), $upozila);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('porcha_processing.service.mouza_option_manager')->update($upozila);

                if ($mode == "edit") {
                    $this->addFlash('success',$this->get('translator')->trans('Upozila updated'));
                } else {
                    $this->addFlash('success',$this->get('translator')->trans('Upozila added'));
                }

                return $this->redirect($this->generateUrl('upozila_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('upozila_create');
        $data['mode'] = $mode;
        if ($mode == "edit") {
            $data['form_action'] = $this->generateUrl('upozila_update', array('id' => $upozila->getId()));
            $data['title'] = 'Edit Upozila';
        }

        return $this->render('PorchaProcessingBundle:MouzaOption:update_upozila.html.twig', $data);
    }

    public function upozilaListAction(Request $request)
    {
        $document = new Document();
        $form = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('app.model.data_import_manager')->importUpozilas($document);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));
                return $this->redirect($this->generateUrl('upozila_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 100);

        $upozilas = $this->get('porcha_processing.service.mouza_option_manager')->getUpozilaList($request->query->all());
        $data['upozilas']  = $this->get('knp_paginator')->paginate($upozilas, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:MouzaOption:upozila_list_sub.html.twig', $data);
        } else {
            $data['form'] = $form->createView();
            $data['form_action'] = $this->generateUrl('upozila_list');
            $data['districts'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:District')->findBy(array('deleted' => 0));
            return $this->render('PorchaProcessingBundle:MouzaOption:upozila_list.html.twig', $data);
        }
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

    public function comboThanaAction($districtId)
    {
        $district = $this->getDoctrine()->getRepository('PorchaProcessingBundle:District')->findOneBy(array('id' => $districtId, 'approved' => 1, 'deleted' => 0));
        $thanas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Thana')->findBy(array('district' => $district, 'approved' => 1, 'deleted' => 0));
        $ret = [];
        foreach ($thanas as $thana) {
            $ret[] = ['id' => $thana->getId(), 'text' => $thana->getName() ];
        }
        return new JsonResponse($ret);
    }

    public function comboUpozilaAction($districtId)
    {
        $district = $this->getDoctrine()->getRepository('PorchaProcessingBundle:District')->findOneBy(array('id' => $districtId, 'approved' => 1, 'deleted' => 0));
        $upozilas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Upozila')->findBy(array('district' => $district, 'approved' => 1, 'deleted' => 0), array('name' => 'asc'));
        $ret = [];
        foreach ($upozilas as $upozila) {
            $ret[] = ['id' => $upozila->getId(), 'text' => $upozila->getName() ];
        }
        return new JsonResponse($ret);
    }

    public function comboMouzaAction($type = 'upozila', $id)
    {
        $upozila = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Upozila')->findOneBy(array('id' => $id, 'approved' => 1, 'deleted' => 0));
        $mouzas = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('upozila' => $upozila, 'approved' => 1, 'deleted' => 0), array('name' => 'asc'));

        $ret = [];
        foreach ($mouzas as $mouza) {
            $ret[] = ['id' => $mouza->getId(), 'text' => $mouza->getName() ];
        }
        return new JsonResponse($ret);
    }

    public function mouzaVolumesAction(Mouza $mouzaId)
    {
        $volumes = $this->get('porcha_processing.service.volume_manager')->getVolumesByMouza($mouzaId);

        $ret = [];
        foreach ($volumes as $volume) {
            $ret[] = ['id' => $volume->getId(), 'text' => $volume->getVolumeNo() ];
        }
        return new JsonResponse($ret);
    }

    public function comboMouzaJlAction($upozilaId, $surveyId)
    {
        $survey = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->find($surveyId);
        $qb = $this->getDoctrine()->getRepository('PorchaProcessingBundle:JLNumber')->createQueryBuilder('j');
        $qb->join('j.mouza', 'm');
        $qb->where('m.upozila = :upozila')->setParameter('upozila', $upozilaId);
        $qb->andWhere('m.approved = :approved')->setParameter('approved', true);
        $qb->andWhere('j.surveyType = :type')->setParameter('type', $survey->getType());
        $mouzas = $qb->getQuery()->getResult();

        $ret = [];
        foreach ($mouzas as $mouza) {
            $ret[] = ['id' => $mouza->getId(), 'text' => $mouza->getMouza()->getName() . ' - ' . $mouza->getName()];
        }
        return new JsonResponse($ret);
    }

    public function upozilaMouzasAction($upozilas) {

        $qb = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->createQueryBuilder('m');
        $qb->where('m.upozila IN (:upozilas)')->setParameter('upozilas', array_values(explode(',', $upozilas)));
        $qb->orderBy('m.name', 'asc');
        $rows = $qb->getQuery()->getResult();

        $data = array();
        foreach ($rows as $row) {
            $data [] = '<option value="' . $row->getId() . '">' . $row->getName() . '</option>';
        }

        return new JsonResponse($data);
    }

    public function mouzaPastInfoAction($surveyType, $mouzaId)
    {
        $ret = $this->get('porcha_processing.service.mouza_option_manager')->getMouzaPastInfo($surveyType, $mouzaId);
        return new JsonResponse(array('district' => $ret['district'], 'thana' => $ret['thana']));
    }

    /**
     * @JMS\Secure(roles="ROLE_MOUZA_OPTION_MANAGEMENT")
     */
    public function updateThanaAction(Request $request, Thana $thana_ = null)
    {
        $mode = "add";
        $thana = new Thana();
        if ($thana_) {
            $thana = $thana_;
            $mode = "edit";
        }

        $form = $this->createForm(new ThanaType($this->getUser(), $this->get('security.authorization_checker')->isGranted('ROLE_GEOGRAPHICAL_INFO_APPROVED')), $thana);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('porcha_processing.service.mouza_option_manager')->update($thana);

                if ($mode == "edit") {
                    $this->addFlash('success', $this->get('translator')->trans('Thana updated'));
                } else {
                    $this->addFlash('success', $this->get('translator')->trans('Thana added'));
                }

                return $this->redirect($this->generateUrl('thana_list'));
            }
        }

        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('thana_create');
        $data['mode'] = $mode;
        if ($mode == "edit") {
            $data['form_action'] = $this->generateUrl('thana_update', array('id' => $thana->getId()));
            $data['title'] = 'Edit Thana';
        }

        $data['importForm'] = $this->createImportForm(new Document())->createView();
        return $this->render('PorchaProcessingBundle:MouzaOption/thana:create_update.html.twig', $data);
    }

    public function thanaListAction(Request $request)
    {
        $document = new Document();
        $importForm = $this->createImportForm($document);

        if ($request->isMethod('post')) {
            $importForm->handleRequest($request);

            if ($importForm->isValid()) {

                $this->get('porcha_processing.service.mouza_option_manager')->importThanas($document);

                $this->addFlash('success',$this->get('translator')->trans('Data imported'));

                return $this->redirect($this->generateUrl('thana_list'));
            }
        }

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $mouzas = $this->get('porcha_processing.service.mouza_option_manager')->getThanaList($request->query->all());
        $data['thanas']  = $this->get('knp_paginator')->paginate($mouzas, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:MouzaOption/thana:list_sub.html.twig', $data);
        } else {
            $data['importForm'] = $importForm->createView();
            $data['form_action'] = $this->generateUrl('thana_list');
            return $this->render('PorchaProcessingBundle:MouzaOption/thana:list.html.twig', $data);
        }
    }

    public function jlnumberByMouzaAction($mouzaId, $surveyType) {

        $row = $this->get('porcha_processing.service.mouza_option_manager')->getJlnumberByMouza($mouzaId, $surveyType);
        return new JsonResponse(array('id' => $row->getId(), 'name' => $row->getName()));
    }

    public function comboJlNoAction($surveyId, $mouzaId)
    {
        $survey = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->find($surveyId);
        $jlNo = $this->getDoctrine()->getRepository('PorchaProcessingBundle:JLNumber')->findOneBy(array('mouza' => $mouzaId, 'surveyType' => $survey->getType(), 'approved' => 1, 'deleted' => 0));

        return new Response(empty($jlNo) ? '' : $jlNo->getName());
    }
}
