<?php

namespace PorchaProcessingBundle\EventListener;

use AppBundle\Service\SMSTransporter;
use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\EntityManager;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\SmsLog;
use PorchaProcessingBundle\Event\ServiceRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SMSSentListener
{
    use EntityAssistant;

    /**
     * @var EntityManager
     */
    protected $em;

    /** @var SMSTransporter */
    protected $smsTransporter;

    public function __construct(EntityManager $entityManager, SMSTransporter $SMSTransporter)
    {
        $this->em = $entityManager;
        $this->smsTransporter = $SMSTransporter;
    }

    public function onServiceRequestCreated(ServiceRequestEvent $serviceRequestEvent)
    {
        $serviceRequest = $serviceRequestEvent->getServiceRequest();

        $message = $this->getMessage($serviceRequest, 'newApplication');
        $this->smsTransporter->send($this->sanitizePhoneNumber($serviceRequest->getContactNumber()), $message);
        $this->log($serviceRequest, $message);
    }

    public function onServiceRequestUpdated(ServiceRequestEvent $serviceRequestEvent)
    {
        $serviceRequest = $serviceRequestEvent->getServiceRequest();
        $oldServiceRequest = $serviceRequestEvent->getOldServiceRequest();

        if ($serviceRequest->getEstimateDeliveryAt()->format('Y-m-d') != $oldServiceRequest->getEstimateDeliveryAt()->format('Y-m-d')) {
            $message = $this->getMessage($serviceRequest, 'changeDeliveryDate');
            $this->smsTransporter->send($this->sanitizePhoneNumber($serviceRequest->getContactNumber()), $message);
            $this->log($serviceRequest, $message);
        }
    }

    private function getMessage(ServiceRequest $serviceRequest, $messageType)
    {

        $messages = $this->em->getRepository('AppBundle:SmsSetting')->getSmsMessages($serviceRequest->getOffice(), $serviceRequest->getType());
        if(!isset($messages[$messageType])){
            return ' ';
        }
        $message = $messages[$messageType];
        $message = str_replace(
            array('%id%', '%date%'),
            array($serviceRequest->getId(), $serviceRequest->getEstimateDeliveryAt()->format('Y-m-d')),
            $message
        );

        return $message;
    }

    private function log(ServiceRequest $serviceRequest, $message)
    {
        $log = new SmsLog();
        $log->setServiceRequest($serviceRequest);
        $log->setMessage($message);
        $this->em->persist($log);
        $this->em->flush();
    }
}
