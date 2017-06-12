<?php

namespace PorchaProcessingBundle\Controller;

use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\KhatianPage;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Entity\OfficeTemplate;
use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Form\KhatianPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ArchiveController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function browseListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $volumes = $this->get('porcha_processing.service.archive_manager')->getBrowseList($request->query->all());
        $data['volumes']  = $this->get('knp_paginator')->paginate($volumes, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Archive:browse_list_sub.html.twig', $data);
        } else {
            $data['search_url'] = $this->generateUrl('archive_browse_list');
            $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));
            $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
            $data['upozilas'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Upozila')->findBy(array('approved' => 1, 'deleted' => 0));
            $data['thanas'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Thana')->findBy(array('approved' => 1, 'deleted' => 0));
            $data['mouzas'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Mouza')->findBy(array('approved' => 1, 'deleted' => 0));
            return $this->render('PorchaProcessingBundle:Archive:browse_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function searchListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatianLogs = $this->get('porcha_processing.service.archive_manager')->getSearchKhatianList($request->query->all());
        $data['khatianLogs']  = $this->get('knp_paginator')->paginate($khatianLogs, $page, $perPage);

        if ($request->isXmlHttpRequest()) {
            return $this->render('PorchaProcessingBundle:Archive:search_list_sub.html.twig', $data);
        } else {
            $data['search_url'] = $this->generateUrl('archive_search_list');
            $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));
            $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
            return $this->render('PorchaProcessingBundle:Archive:search_list.html.twig', $data);
        }
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function khatianListAction(Request $request, Volume $volume)
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
        $data['info'] = $this->get('porcha_processing.service.volume_manager')->getVolumeDatails($volume);

        $this->get('session')->set('referer', $request->getRequestUri());

        return $this->renderKhatianList($request->isXmlHttpRequest(), $data);
    }

    private function renderKhatianList($isXmlHttpRequest = false, $data)
    {
        if ($isXmlHttpRequest) {
            return $this->render('PorchaProcessingBundle:Archive:khatian_list_sub.html.twig', $data);
        }
        return $this->render('PorchaProcessingBundle:Archive:khatian_list.html.twig', $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function porchaRequestArchiveListAction(Request $request)
    {
        $serviceType = 'PORCHA_REQUEST';
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('print') == 'yes' ? 99999999 : $this->getParameter('record_per_page');
        $requestAll = $request->query->all();

        $requestAll['ff']['o.id'] = $this->getUser()->getOffice()->getId();
        $serviceRequest = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestList($requestAll, $serviceType, $delivered = true);
        $data['serviceRequest']  = $this->get('knp_paginator')->paginate($serviceRequest, $page, $perPage);
        $data['sl'] = ($page - 1) * $perPage + 1;
        $data['serviceType'] = $serviceType;
        $data['statistics'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceRequestStatistics($requestAll, $serviceType);

        $template = 'PorchaProcessingBundle:Service/PorchaRequest:archive_application_list.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'PorchaProcessingBundle:Service/PorchaRequest:archive_application_list_sub.html.twig';
        }
        return $this->render($template, $data);
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function khatianSearchAction(Request $request) {

        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('per-page', 25);

        $khatians = $this->get('porcha_processing.service.archive_manager')->khatianSearch($request->query->all());
        $data['khatians']  = $this->get('knp_paginator')->paginate($khatians, $page, $perPage);
        $data['query_params'] = http_build_query($this->searchParams($request));

        if ($this->get('request')->isXmlHttpRequest()) {

            if ($this->isGranted('ROLE_MARK_KHATIAN_AS_NON_DELIVERABLE')) {
                $requestAll = $request->request->all();
                if (isset($requestAll['multiAction'])) {

                    if (!$this->isCsrfTokenValid('archived_khatian_action', $request->request->get('_token'))) {
                        return new Response('FAILED');
                    }

                    $this->get('porcha_processing.service.archive_manager')->archivedKhatinMultiAction(
                        $request->request->get('multiAction'),
                        $request->request->get('restricted'),
                        $request->request->get('entryOperator', 0)
                    );
                }
            }

            return $this->render('PorchaProcessingBundle:Archive:khatian_search_sub.html.twig', $data);
        }

        $data['search_url'] = $this->generateUrl('archive_search_khatian');
        $data['surveys'] = $this->getDoctrine()->getRepository('PorchaProcessingBundle:Survey')->findBy(array('approved' => 1));
        $data['districts'] = $this->get('porcha_processing.service.mouza_option_manager')->getRelatedDistricts();
        $data['entryOperators'] = $this->get('porcha_processing.service.archive_manager')->entryOperatorsForKhatianReCorrection();

        return $this->render('PorchaProcessingBundle:Archive:khatian_search.html.twig', $data);
    }

    private function searchParams($request) {

        $ret = array();
        $ss = $request->query->get('ss');
        $ret['dag_nong'] = $ss['kp.dagNong'];
        switch ($request->query->get('sf', 'otsDokholkar')) {
            case 'otsDokholkar':
                $ret['ots_dokholkar'] = $request->query->get('sv', '');
                $ret['ots_dokholkar_2'] = $request->query->get('sv', '');
                break;
            case 'upsDokholkar':
                $ret['ups_dakholkar_sangkhipto'] = $request->query->get('sv', '');
                break;
        }

        return $ret;
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function archivedKhatianViewAction(Request $request, Khatian $khatian)
    {
        if ($khatian->isDisplayRestricted() && !$this->isGranted('ROLE_VIEW_RESTRICTED_KHATIAN')) {
            throw new HttpException(412, $this->get('translator')->trans("You dont have permission to view this Khatian"));
        }

        return $this->khatianVersion($request, $khatian->getLastVersion());
    }

    /**
     * @JMS\Secure(roles="ROLE_VIEW_ARCHIVE")
     */
    public function archivedKhatianVersionViewAction(Request $request, KhatianVersion $khatianVersion)
    {
        return $this->khatianVersion($request, $khatianVersion);
    }

    private function khatianVersion(Request $request, KhatianVersion $khatianVersion) {

        $khatianManager = $this->get('porcha_processing.service.khatian_manager');

        $khatian = $khatianVersion->getKhatian();
        $khatianPageEntities = $khatianManager->getKhatianPages($khatianVersion);
        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);
        $khatianLog = $khatianManager->getKhatianLogByKhatianVersion($khatianVersion);

        return $this->render('@PorchaProcessing/Khatian/readonly_view_khatian.html.twig', array(
            'khatianPages' => $khatianPages,
            'pagination' => $pagination,
            'khatian' => $khatian,
            'khatianLog' => $khatianLog,
            'query_params' => $request->query->all(),
            'non_deliverables' => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            'non_deliverable_template' => $this->renderView('@PorchaProcessing/Khatian/nondeliverable.html.twig', array('survey_name' => $khatian->getVolume()->getSurvey()->getName())),
            'khatian_versions' => $khatianManager->getKhatianVersions($khatian),
            'this_version_id' => $khatianVersion->getId()
        ));
    }


}
