<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Office;
use PorchaProcessingBundle\Util\PlaceHolders;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function indexAction()
    {
//         $type = $this->getUser()->getOffice()->getType();
//
//        $data['currentUser'] = $this->getUser();
//        $data['currentUserOffice'] = $this->get('session')->get('userOffice');
//
//        if(strtoupper($type) == 'UDC'){
//          //  $udcOffices = $this->getUdcOffices();
//        //    $udcOffices = $this->getUser()->getOffice()->getId();
//
//           // $data = $this->loadDashboardData($udcOffices, $data);
//            return $this->redirect($this->generateUrl('porcha_udc_request_list'));
//           // return $this->render('AppBundle:Dashboard:udc_office.html.twig', $data);
//
//        } elseif(strtoupper($type) == 'DC'){
//
//            $data['khatianStatistics'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatisticsByStatus();
//            $data['khatianStatusName'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatusNames();
//
//            return $this->render('AppBundle:Dashboard:dc_office.html.twig', $data);
//        } elseif(strtoupper($type) == 'AC_LAND') {
//            return $this->redirect($this->generateUrl('porcha_correction_request_ac_land_list'));
//        }else {
//            $data['khatianStatistics'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatisticsByStatus();
//            $data['khatianStatusName'] = $this->get('porcha_processing.service.khatian_manager')->getKhatianStatusNames();
//            return $this->render('AppBundle:Dashboard:dc_office.html.twig', $data);
//        }
        return $this->render('AppBundle:Dashboard:dc_office.html.twig');
    }



    public function dashboardDataAction(Request $request)
    {
        $officeType = $this->getUser()->getOffice()->getType();

        $today = $request->query->get('date', date('Y-m-d'));
        $data = $this->get('porcha_processing.service.dashboard_manager')
            ->dashboardDataStatusWiseSummary($today, $this->getUser()->getOffice());

        $serviceRequestData = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->porchaRequestDashboardStatistics($today, $this->getUser()->getOffice());

        return new JsonResponse(
            array(
                'table' => $this->renderView('@PorchaProcessing/Report/EntryStatistics/_table.html.twig', array(
                    'data' => $data['table'],
                    'today' => new \DateTime($today),
                    'serviceRequestData' => $serviceRequestData
                )),
                'chart' => $data['chart']
            )
        );
    }public function udcDashboardDataAction(Request $request)
    {
        $officeType = $this->getUser()->getOffice()->getType();

        $today = $request->query->get('date', date('Y-m-d'));
        $udcOffices = $this->getUser()->getOffice()->getId();
        $udcStatistics = $this->loadDashboardData($udcOffices);
        // $serviceRequestData = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->porchaRequestDashboardStatistics($today, $this->getUser()->getOffice());
//var_dump($udcStatistics);die;
        return new JsonResponse(
            array(
                'table' => $this->renderView('@PorchaProcessing/Report/Udc/_table.html.twig', array(
                    'serviceRequestData' => $udcStatistics
                )),
//                'chart' => $data['chart']
            )
        );
    }
    public function udcServiceFilterByAppIdAction(Request $request)
    {
        $serviceType = $request->query->get('optionsRadios');

        $applicationId = $request->query->get('application_id');
       /* if(is_int($request->query->get('application_id'))){
            $applicationId = $request->query->get('application_id');
        }else{
            $applicationId = $request->query->get('application_id');
            $this->addFlash('error', 'এখানে শুধু মাত্র অ্যাপ্লিকেশান আইডি  দিতে হবে । কোন  অক্ষর ব্যাবহার করা যাবে না । ');
            return $this->render('AppBundle:Udc:serviceRequestdetail.html.twig',
                array(
                    'serviceRequests' => array(),
                    'type' => $serviceType,
                    'applicationId' =>$applicationId

                ));
        }*/

        $serviceRequest = $this->getDoctrine()
                               ->getRepository('PorchaProcessingBundle:ServiceRequest')
                               ->getServiceRequestFilterByApplicationId($applicationId, $serviceType,$this->getUser());

        return $this->render('AppBundle:Udc:serviceRequestdetail.html.twig',
            array(
                'serviceRequests' => $serviceRequest?$serviceRequest[0]:array(),
                'type' => $serviceType,
                'applicationId' =>$applicationId

            ));


    }

    public function getUdcOffices() {

        $office = $this->getUser()->getOffice();

        /**@var Office $office*/
        $offices = array();
        if ($office->getChildren()) {
            foreach ($office->getChildren() as $children) {
                $offices[] = $children->getId();
            }
        }
        return $offices;
    }

    /**
     * @param $udcOffices
     * @return mixed
     */
    private function loadDashboardData($udcOffices)
    {
        $data['ApplicationStatistics'] = $this->get('app.service.udc_manager')->getApplicationStatisticsByStatus(
            $udcOffices
        );
        $data['ApplicationType']       = $this->get('app.service.udc_manager')->getApplicationStatisticsByType(
            $udcOffices
        );

        $row = array();
        foreach ($data['ApplicationStatistics'] as $key => $statistics) {
            $row[$key]['DELIVERED'] = isset($statistics['DELIVERED']) ? $statistics['DELIVERED'] : '';
            $row[$key]['PENDING']   = isset($statistics['PENDING']) ? $statistics['PENDING'] : '';
            $row[$key]['total']     = $data['ApplicationType'][$key];

        }

        return $row;
    }
}