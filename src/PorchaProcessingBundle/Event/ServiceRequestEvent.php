<?php

namespace PorchaProcessingBundle\Event;

use PorchaProcessingBundle\Entity\ServiceRequest;
use Symfony\Component\EventDispatcher\Event;

class ServiceRequestEvent extends Event
{
    protected $serviceRequest;
    protected $oldServiceRequest;

    public function __construct(ServiceRequest $serviceRequest, ServiceRequest $oldServiceRequest = null)
    {
        $this->serviceRequest = $serviceRequest;
        $this->oldServiceRequest = $oldServiceRequest;
    }

    public function getServiceRequest()
    {
        return $this->serviceRequest;
    }

    public function getOldServiceRequest()
    {
        return $this->oldServiceRequest;
    }
}
