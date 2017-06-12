<?php

namespace PorchaProcessingBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Mouza;
use PorchaProcessingBundle\Entity\Upozila;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AddMouzasFieldSubscriber implements EventSubscriberInterface
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

    private function addMouzaField(FormInterface $form, $upozilaId = null, $mouzaId = null)
    {
        $formOptions = array(
            'class'         => 'PorchaProcessingBundle:Mouza',
            'empty_value'   => 'Select',
            'attr'          => array(
                'class' => 'mouza_selector',
            ),
            'query_builder' => function (EntityRepository $repository) use ($upozilaId, $mouzaId) {
                $qb = $repository->createQueryBuilder('mouza');
                if (!$upozilaId && !$mouzaId) {
                    $qb->where('mouza.id = :mouza')->setParameter('mouza', 0);
                } else if ($upozilaId) {
                    $qb->join('mouza.upozila','upozila');
                    $qb->where('upozila.id = :upozila')->setParameter('upozila', $upozilaId);
                }else if ($mouzaId) {
                    $qb->where('mouza.id = :mouza')->setParameter('mouza', $mouzaId);
                }

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
            $this->addMouzaField($form);
            return;
        }

        $accessor    = PropertyAccess::createPropertyAccessor();

        /** @var Mouza $mouza */
        $mouza        = $accessor->getValue($data, $this->propertyPath);
        $upozilaId = ($mouza) ? $mouza->getUpozila()->getId() : null;
        $this->addMouzaField($form, $upozilaId);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $mouza = array_key_exists('mouza', $data) ? $data['mouza'] : null;

        $this->addMouzaField($form, null, $mouza);
    }
}
