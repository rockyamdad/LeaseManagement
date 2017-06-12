<?php

namespace PorchaProcessingBundle\EventListener;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AddUpozilaFieldSubscriber implements EventSubscriberInterface
{
    private $propertyPath;
    private $isDisabled;

    public function __construct($isDisabled = false)
    {
        $this->propertyPath = 'upozila';
        $this->isDisabled = $isDisabled;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA  => 'preSetData',
            FormEvents::PRE_SUBMIT    => 'preSubmit'
        );
    }

    private function addUpozilaField(FormInterface $form, $district_id)
    {
        $formOptions = array(
            'class'         => 'PorchaProcessingBundle:Upozila',
            'empty_value'   => 'Select',
            'attr'          => array(
                'class' => 'upozila_selector',
            ),
            'disabled' => ($this->isDisabled) ? true : false,
            'query_builder' => function (EntityRepository $repository) use ($district_id) {
                $qb = $repository->createQueryBuilder('upozila')
                    ->innerJoin('upozila.district', 'district')
                    ->where('district.id = :district')
                    ->setParameter('district', $district_id)
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

        /** @var Upozila $upozila */
        $upozila        = $accessor->getValue($data, $this->propertyPath);
        $dictrict_id = ($upozila) ? $upozila->getDistrict()->getId() : null;
        $this->addUpozilaField($form, $dictrict_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $district_id = array_key_exists('district', $data) ? $data['district'] : null;

        $this->addUpozilaField($form, $district_id);
    }
}
