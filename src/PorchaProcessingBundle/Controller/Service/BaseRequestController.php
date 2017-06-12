<?php

namespace PorchaProcessingBundle\Controller\Service;

use AppBundle\Entity\Office;
use JMS\SecurityExtraBundle\Annotation as JMS;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseRequestController extends Controller
{
    /**
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_MANAGE")
     */
    public function commonPrintAction(ServiceRequest $serviceRequest)
    {
        $detailEntities = $this->getDetailEntities($serviceRequest);
        return $this->render('@PorchaProcessing/Service/PorchaRequest/common_service_request_print.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'detail' => $detailEntities ? $detailEntities[0] : null,
            'office' => $serviceRequest->getOffice()->getType() == 'UDC' ? $serviceRequest->getOffice()->getParent() : $serviceRequest->getOffice(),  
            'additionalFee' => $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestAdditionalFee')->findBy(array('serviceRequest' => $serviceRequest)),
        ));
    }

    protected function getDetailEntities(ServiceRequest $serviceRequest)
    {
        $detailEntities = null;
        if (in_array($serviceRequest->getType(), array('PORCHA_REQUEST', 'MOUZA_MAP'))) {
            $detailEntities = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestPorcha')->findBy(array('serviceRequest' => $serviceRequest));
        }

        if (in_array($serviceRequest->getType(), array('CASE_COPY'))) {
            $detailEntities = $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestCaseCopy')->findBy(array('serviceRequest' => $serviceRequest));
        }

        return $detailEntities;
    }

    public function getOwnOfficeUpozillas()
    {
        $em = $this->getDoctrine();
        return $em->getRepository('PorchaProcessingBundle:Upozila')
            ->findBy(array('district' => $em->getRepository('PorchaProcessingBundle:District')->findOneBy(
                array('geocode' => $this->getUser()->getOffice()->getDistrict()->getGeocode())
            )));
    }

    /**
     * @JMS\Secure(roles="ROLE_SERVICE_REQUEST_MANAGE")
     */
    public function serviceRequestCommonView(ServiceRequest $serviceRequest)
    {
        $detailEntities = $this->getDetailEntities($serviceRequest);
        return $this->render('@PorchaProcessing/Service/common_view.html.twig', array(
            'serviceRequest' => $serviceRequest,
            'additionalFees' => $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequestAdditionalFee')->findBy(array('serviceRequest' => $serviceRequest)),
            'detailEntity' => $detailEntities ? $detailEntities[0] : null,
        ));
    }

    protected function savePorchaRequestPostalAddressInSesion($data, ServiceRequest $serviceRequest)
    {
        if (isset($data['save-and-new'])) {
            $session = array(
                'name' => $serviceRequest->getName(),
                'contactNumber' => $serviceRequest->getContactNumber(),
                'nid' => $serviceRequest->getNid(),
                'email' => $serviceRequest->getEmail(),
                'postal_district' => ($serviceRequest->getDistrict()) ? $serviceRequest->getDistrict()->getId() : '',
                'postal_upozila' => ($serviceRequest->getUpozila()) ? $serviceRequest->getUpozila()->getId() : '',
                'postal_zip_code' => $serviceRequest->getPostalCode(),
                'postal_area' => $serviceRequest->getArea(),
                'postal_road_no' => $serviceRequest->getRoadNo(),
                'postal_house_no' => $serviceRequest->getHouseNo(),
                'postal_ongoing_care' => $serviceRequest->getOngoingCare(),
            );

            $this->get('request')->getSession()->set('porcha_request_applicant_info', $session);
        }
    }

    protected function setPorchaRequestPostalAddressFromSesion(ServiceRequest $serviceRequest)
    {
        if ($sessionData = $this->get('request')->getSession()->get('porcha_request_applicant_info')) {
            $serviceRequest->setName($sessionData['name']);
            $serviceRequest->setContactNumber($sessionData['contactNumber']);
            $serviceRequest->setNid($sessionData['nid']);
            $serviceRequest->setEmail($sessionData['email']);
            $serviceRequest->setDistrict($this->getDoctrine()->getRepository('AppBundle:District')->find($sessionData['postal_district']));
            $serviceRequest->setUpozila($this->getDoctrine()->getRepository('AppBundle:Upozila')->find($sessionData['postal_upozila']));
            $serviceRequest->setPostalCode($sessionData['postal_zip_code']);
            $serviceRequest->setArea($sessionData['postal_area']);
            $serviceRequest->setRoadNo($sessionData['postal_road_no']);
            $serviceRequest->setHouseNo($sessionData['postal_house_no']);
            $serviceRequest->setOngoingCare($sessionData['postal_ongoing_care']);
        }
    }

    protected function getServiceFee(Office $office, ServiceRequest $serviceRequest, $requestType = null)
    {
        return $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getServiceFee($office, $serviceRequest, $requestType);
    }

    protected function getSingleServiceFee(Office $office, ServiceRequest $serviceRequest, $requestType = null)
    {
        return $this->getDoctrine()->getRepository('PorchaProcessingBundle:ServiceRequest')->getSingleServiceFee($office, $serviceRequest, $requestType);
    }

    protected function setPorcharequestFrom(ServiceRequest $serviceRequest)
    {
        $officeType = $serviceRequest->getOffice()->getType();
        if ($officeType == 'UDC') {
            $serviceRequest->setRequestFrom('UDC');
        } else {
            $serviceRequest->setRequestFrom('DIRECT');
        }
    }

    protected function hasAccess(ServiceRequest $serviceRequest)
    {
        /** @var Office $office */
        $applicationOffice = $serviceRequest->getOffice();
        $applicationParentOffice = $serviceRequest->getOffice()->getParent();
        $office = $this->getUser()->getOffice();
        $officeType = $office->getType();

        if ($officeType == 'UDC' && $applicationOffice->getId() !== $office->getId()) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

        if ($officeType == 'DC' && (
                $applicationOffice->getId() !== $office->getId() ||
                $applicationParentOffice->getId() !== $office->getId()
            )) {
            throw new HttpException(412, $this->get('translator')->trans("Not allowed"));
        }

    }

    protected function dispatch($eventName, $event)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }

    protected function determineWhatCanBeEditServiceRequest(ServiceRequest $serviceRequest, $otherEntity = null)
    {
        $data = $this->getDataForDetermineWhatCanBeEditServiceRequest($serviceRequest);
        if ($otherEntity) {
            $otherEntity->disableFields = $data;
        }
        $serviceRequest->disableFields = $data;
    }

    public function getDataForDetermineWhatCanBeEditServiceRequest(ServiceRequest $serviceRequest)
    {
        $data = array(
            'applicationInfo' => false,
            'applicantInfo' => false,
            'deliveryAddress' => false,
            'paymentInfo' => false,
            'deliveryDate' => false,
        );

        if (!$serviceRequest->getId()) {
            return $data;
        }

        $today = date('Y-m-d');
        $applicationDate = $serviceRequest->getCreatedAt()->format('Y-m-d');
        $status = $serviceRequest->getStatus();
        $requestFrom = $serviceRequest->getRequestFrom();

        if ($status != 'PENDING') {
            $data['applicationInfo'] = true;
        }

        if ($today != $applicationDate || $status != 'PENDING') {
            $data['applicantInfo'] = true;
            $data['deliveryAddress'] = true;
            $data['paymentInfo'] = true;
        }

        if ($requestFrom == 'WEB') {
            $data['applicantInfo'] = true;
            $data['deliveryAddress'] = true;
            $data['paymentInfo'] = true;
            $data['applicationInfo'] = true;
        }

        if (!$this->isGranted('ROLE_CHANGE_DELIVERY_DATE') || !$data['paymentInfo']) {
            $data['deliveryDate'] = true;
        }

        return $data;
    }

    public function getOffices(){
        /** @var Office $office*/
        $office = $this->getUser()->getOffice();

        $offices = array($office->getId());
        if ($office->getChildren()) {
            foreach ($office->getChildren() as $children) {
                $offices[] = $children->getId();
            }
        }
        return $offices;
    }

    protected function isServiceEnabled()
    {
        $office = $this->getUser()->getOffice();
        /**@var Office $office */

        switch (strtoupper($office->getType())) {

            case 'UDC':
                $office = $office->getParent();
                $media = $office->getOfficeSettings()->getApplicationReceiveMedia();
                if (in_array('UDC', $media)) {
                    return true;
                }
                break;
            case 'DC':
                $media = $office->getOfficeSettings()->getApplicationReceiveMedia();
                if (in_array('DIRECT', $media)) {
                    return true;
                }
                break;

        }

        return false;
    }

}