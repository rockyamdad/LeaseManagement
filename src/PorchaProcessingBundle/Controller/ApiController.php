<?php

namespace PorchaProcessingBundle\Controller;

use AppBundle\Entity\District;
use AppBundle\Entity\Division;
use AppBundle\Entity\Upozila;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianVersion;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function sendSurveysAction()
    {

        $surveys = $this->get('porcha_processing.service.api_manager')->getSurveys();
        return new JsonResponse($surveys);
    }

    public function sendUdcListAction()
    {

        $UdcList = $this->get('porcha_processing.service.api_manager')->getUdcList();
        return new JsonResponse($UdcList);
    }

    public function apiResponseAction(Request $request, $data)
    {

//        if (!$request->headers->has('X-ApiKey')) {
//            return new Response('Required API key is missing.', 400);
//        }
//
//        if ($request->headers->get('X-ApiKey') != '123') {
//            return new Response('Unauthorized access.', 401);
//        }

        $response = new JsonResponse($data, 200, [
            'Content-type' => 'application/json'
        ]);

        return $response;
    }

    public function sendDivisionsAction(Request $request)
    {

        $divisions = $this->get('porcha_processing.service.api_manager')->getDivisions();
        return $this->apiResponseAction($request, $divisions);
    }

    public function sendAllDistrictsAction(Request $request)
    {

        $districts = $this->get('porcha_processing.service.api_manager')->getAllDistricts();
        return $this->apiResponseAction($request, $districts);
    }

    public function sendDistrictsAction(Request $request, Division $division)
    {

        $districts = $this->get('porcha_processing.service.api_manager')->getDistricts($division);
        return $this->apiResponseAction($request, $districts);
    }

    public function sendAllPorchaListByUserAction(Request $request, $user)
    {

        $porchaList = $this->get('porcha_processing.service.api_manager')->getPorchaList($user);
        return $this->apiResponseAction($request, $porchaList);
    }

    public function sendAllMouzaMapByUserAction(Request $request, $user)
    {

        $mouzaMapList = $this->get('porcha_processing.service.api_manager')->getMouzaMapList($user);
        return $this->apiResponseAction($request, $mouzaMapList);
    }

    public function sendAllCaseCopyByUserAction(Request $request, $user)
    {

        $caseCopyList = $this->get('porcha_processing.service.api_manager')->getCaseCopyList($user);
        return $this->apiResponseAction($request, $caseCopyList);
    }

    public function sendAllInformationSlipByUserAction(Request $request, $user)
    {

        $informationSlipList = $this->get('porcha_processing.service.api_manager')->getInformationSlipList($user);
        return $this->apiResponseAction($request, $informationSlipList);
    }

    public function sendUpozilasAction(Request $request, District $district)
    {

        $upozilas = $this->get('porcha_processing.service.api_manager')->getUpozilas($district);
        return $this->apiResponseAction($request, $upozilas);
    }

    public function sendMouzasAction(Request $request, Upozila $upozila)
    {
        $mouzas = $this->get('porcha_processing.service.api_manager')->getMouzas($upozila);
        return $this->apiResponseAction($request, $mouzas);
    }

    public function sendVolumesAction(Request $request, Mouza $mouza, $type)
    {
        $volumes = $this->get('porcha_processing.service.api_manager')->getVolumes($mouza, $type);
        return $this->apiResponseAction($request, $volumes);
    }

    public function sendJlnumbersAction(Request $request)
    {

        $jlnumbers = $this->get('porcha_processing.service.api_manager')->getJlnumbers();
        return $this->apiResponseAction($request, $jlnumbers);
    }

    public function sendCourtFeesAction(Request $request, $districtId, $type)
    {
        $office = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->findOneByDistrict($districtId);
        $courtFee = $this->getDoctrine()->getManager()->getRepository('AppBundle:CourtFee')->getCourtFee($office, $type);

        return $this->apiResponseAction($request, $courtFee);
    }

    public function sendKhatianStatusByApplicationIdAction(Request $request, $applicationId)
    {
        $khatianStatus = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:ServiceRequest')->find($applicationId);
        if (!$khatianStatus) {
            $errorMessage = 'কোন রেকর্ড নেই';
            return $this->apiResponseAction($request, $errorMessage);
        }
        return $this->apiResponseAction($request, $khatianStatus->getStatus());
    }
    public function sendKhatianIdByApplicationIdAction(Request $request, $applicationId)
    {
        $khatianObject = $this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findKhatianIdByApplicationId($applicationId);
        if($khatianObject){
        $khatianId=$this->getDoctrine()->getManager()->getRepository('PorchaProcessingBundle:Khatian')->findKhatianIdByKhatianNo($khatianObject[0]);
            if($khatianId){
            return $this->apiResponseAction($request, $khatianId[0]['id']);
            }
            else return $this->apiResponseAction($request, 'No Result Found');
        }
        else return $this->apiResponseAction($request, 'No Result Found');
    }

    public function sendCourtFeesCaseCopyAction(Request $request, $districtId, $type)
    {
        $office = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->findOneByDistrict($districtId);
        $courtFee = $this->getDoctrine()->getManager()->getRepository('AppBundle:CourtFee')->getSingleCourtFee($office, $type);

        return $this->apiResponseAction($request, $courtFee);
    }

    public function sendCourtFeesMouzaApplicationAction(Request $request, $districtId, $type)
    {
        $office = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->findOneByDistrict($districtId);
        $courtFee = $this->getDoctrine()->getManager()->getRepository('AppBundle:CourtFee')->getCourtFee($office, $type);

        return $this->apiResponseAction($request, $courtFee);
    }

    public function sendCourtFeesInformationApplicationAction(Request $request, $districtId, $type)
    {
        $office = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->findOneByDistrict($districtId);
        $courtFee = $this->getDoctrine()->getManager()->getRepository('AppBundle:CourtFee')->getSingleCourtFee($office, $type);

        return $this->apiResponseAction($request, $courtFee);
    }

    public function sendDeliveryFeesAction(Request $request, $districtId)
    {
        $office = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->findOneByDistrict($districtId);
        $deliveryFee = $this->getDoctrine()->getManager()->getRepository('AppBundle:Office')->getDeliveryFee($office);

        return $this->apiResponseAction($request, $deliveryFee);
    }

    public function sendKhatianViewAction(Request $request, Khatian $khatian)
    {
        $viewKhatian = $this->archivedKhatianView($request, $khatian);
        return $this->apiResponseAction($request, $viewKhatian);

    }

    public function archivedKhatianView(Request $request, Khatian $khatian)
    {
        /*if ($khatian->isDisplayRestricted()) {
            if (!$this->isGranted('ROLE_VIEW_RESTRICTED_KHATIAN')) {
                throw $this->createAccessDeniedException($this->get('translator')->trans("You dont have permission to view this Khatian"));
            }
        }*/

        return $this->khatianVersion($request, $khatian->getLastVersion());
    }

    private function khatianVersion(Request $request, KhatianVersion $khatianVersion)
    {
        $khatianManager = $this->get('porcha_processing.service.khatian_manager');
        $khatian = $khatianVersion->getKhatian();

        $khatianPageEntities = $khatianManager->getKhatianPages($khatianVersion);

        $khatianPages = $khatianManager->getKhatianPagePrintView($khatianPageEntities, $khatian);
        $pagination = $khatianManager->prepareKhatianPagePaginationForPrintView($khatianPages);
        $khatianLog = $khatianManager->getKhatianLogByKhatianVersion($khatianVersion);

        return $this->renderView('@PorchaProcessing/Khatian/readonly_view_khatian.html.twig', array(
            'khatianPages'             => $khatianPages,
            'pagination'               => $pagination,
            'khatian'                  => $khatian,
            'khatianLog'               => $khatianLog,
            'query_params'             => $request->query->all(),
            'non_deliverables'         => $khatianManager->getNonDeliverableMessages($khatianLog->getKhatianVersion()->getNonDeliverable()),
            'non_deliverable_template' => $this->renderView('@PorchaProcessing/Khatian/nondeliverable.html.twig', array('survey_name' => $khatian->getVolume()->getSurvey()->getName())),
            'khatian_versions'         => $khatianManager->getKhatianVersions($khatian),
            'this_version_id'          => $khatianVersion->getId()
        ));
    }

    public function sendStatisticsMonthlyAction(Request $request)
    {
        $stats = $this->get('porcha_processing.service.api_manager')->getLastMonthStatistics();
        return $this->apiResponseAction($request, $stats);
    }

}
