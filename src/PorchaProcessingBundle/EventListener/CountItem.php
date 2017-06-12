<?php

namespace PorchaProcessingBundle\EventListener;

use AppBundle\Entity\Office;
use AppBundle\Entity\Udc;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\ServiceRequest;
use PorchaProcessingBundle\Entity\ServiceRequestPorcha;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountItem
{
    /** @var  ContainerInterface */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        return false;
        $entity = $args->getEntity();

        if ($entity instanceof Upozila) {

            $this->getManager()->handleUpozilaCreate($entity);
        }
        if ($entity instanceof Mouza) {

           $this->getManager()->handleMouzaCreate($entity);
        }
        if ($entity instanceof ServiceRequest) {

            $this->getManager()->handleAllApplicationCreate($entity);
        }
        if ($entity instanceof ServiceRequestPorcha) {

            $this->getManager()->handlePorchaMouzaApplicationCreate($entity);
        }
        if ($entity instanceof Office) {

            $this->getManager()->handleUdcCreate($entity);
        }
        if ($entity instanceof Khatian) {

           $this->getManager()->handleKhatianCreate($entity);
        }

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * @return \PorchaProcessingBundle\Service\ApiManager
     */
    protected function getManager()
    {
        return $this->container->get('porcha_processing.service.api_manager');
    }
}
