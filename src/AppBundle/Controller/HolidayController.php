<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Holiday;
use AppBundle\Form\HolidayType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\HttpFoundation\Response;

class HolidayController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_MANAGING_HOLIDAY_CALENDAR")
     */
    public function holidayListAction(Request $request) {

        $page = $request->query->get('page', 1);
        $holidays = $this->get('app.service.office_manager')->getHolidayList($request->query->all());
        $data['holidays']  = $this->get('knp_paginator')->paginate($holidays, $page, 50);


        $data['months']= array(1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December');
        $data['dcOffices'] = $this->get('app.service.office_manager')->getOfficeListByType('DC');
        $data['current_user_office'] = $this->getUser()->getOffice();

        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Holiday:holiday_list_sub.html.twig', $data);
        } else {
            $data['search_url'] = $this->generateUrl('holiday_list');
            return $this->render('AppBundle:Holiday:holiday_list.html.twig', $data );
        }
    }
    public function yearSummeryAction(Request $request){

        $data['holidayoverview'] = $this->get('app.service.office_manager')->holidayOverview($request->query->get('year'));
        return $this->render('AppBundle:Holiday:holiday_year_summery_list.html.twig', $data );
    }
    public function createHolidayAction(Request $request)
    {
        $holiday = new Holiday();
        $officeType = $this->getUser()->getOffice()->getType();
        $form = $this->createForm(new HolidayType($officeType), $holiday);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $request->request->all();
               $msg = $this->get('app.service.office_manager')->saveHoliday($data);
                if($msg['error']){
                    $this->addFlash("error", $this->get('translator')->trans($msg['error']));
                    return $this->redirect($this->generateUrl('create_holiday'));
                }else{
                    $this->addFlash("success", $this->get('translator')->trans("Holiday has been Created"));
                    return $this->redirect($this->generateUrl('holiday_list'));
                }
            }
        }

        $data['holiday'] = $holiday;
        $data['form'] = $form->createView();
        $data['form_action'] = $this->generateUrl('create_holiday');
        return $this->render('AppBundle:Holiday:create_holiday.html.twig',$data);
    }

    public function deleteHolidayAction(Holiday $holiday){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Holiday')->find($holiday);
        $current_date = date('Y-m-d');
        $current_user_office = $this->getUser()->getOffice();
        $current_user_office_type = $current_user_office->getType();
        if($current_date < $entity->getDate()->format('Y-m-d')){
            if($current_user_office == $entity->getOffice() && $entity->getOffice() != null){
                $em->remove($entity);
                $this->addFlash("success", $this->get('translator')->trans("This holiday has been deleted"));
            }elseif($current_user_office_type=='MINISTRY' && $entity->getOffice() == null){
                $em->remove($entity);
                $this->addFlash("success", $this->get('translator')->trans("This holiday has been deleted"));
            }else{
                $this->addFlash("error", $this->get('translator')->trans("You are not allowed to remove this holiday"));
            }

        }else{
            $this->addFlash("error", $this->get('translator')->trans("This holiday has already in the past"));
        }

        $em->flush();

        return $this->redirect($this->generateUrl('holiday_list'));
    }

    public function holidayListByTypeAction($year, $type) {

        $holidays =$this->get('app.service.office_manager')->getHolidayListByType($year, $type);
        return $this->render('AppBundle:Holiday:type_wise_holiday_list.html.twig', array(
            'holidays' => $holidays
        ));
    }
}
