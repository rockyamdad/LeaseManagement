<?php

namespace PorchaProcessingBundle\EventListener;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AddMouzaFieldSubscriber implements EventSubscriberInterface
{
    private $propertyPath;

    public function __construct()
    {
        $this->propertyPath = 'mouza';
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => 'preSetData',
            FormEvents::PRE_SUBMIT    => 'preSubmit'
        );
    }

    private function addUpozilaField(FormInterface $form, $upozila_id)
    {
        $formOptions = array(
            'class'         => 'PorchaProcessingBundle:Mouza',
            'empty_value'   => 'Select',
            'attr'          => array(
                'class' => 'mouza_selector',
            ),
            'query_builder' => function (EntityRepository $repository) use ($upozila_id) {
                $qb = $repository->createQueryBuilder('mouza')
                    ->innerJoin('mouza.upozila', 'upozila')
                    ->where('upozila.id = :upozila')
                    ->setParameter('upozila', $upozila_id)
                ;

                return $qb;
            }
        );

        $form->add($this->propertyPath, 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            $this->addUpozilaField($form, 0);
            return;
        }

        $accessor    = PropertyAccess::createPropertyAccessor();

        /** @var Mouza $mouza */
        $mouza        = $accessor->getValue($data, $this->propertyPath);
        $upozila_id = ($mouza) ? $mouza->getUpozila()->getId() : null;
        $this->addUpozilaField($form, $upozila_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $upozila_id = array_key_exists('upozila', $data) ? $data['upozila'] : null;

        $this->addUpozilaField($form, $upozila_id);
    }
}
