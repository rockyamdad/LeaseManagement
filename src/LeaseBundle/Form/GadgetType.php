<?php

namespace LeaseBundle\Form;

use PorchaProcessingBundle\Entity\Mouza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GadgetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $mouza = $options['data']->getMouza();

        $builder
            ->add('orginalOwnerName','text',array(
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('fatherName','text',array(
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('address','textarea',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('caseFileNo','text',array(
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('govtAquiredDate','date',array(
                'widget'=>'single_text',
                'html5'=>false
            ))
            ->add('mouza', 'entity', array(
                'required'=>true,
                'attr' => array('class' => 'form-control select2 input-large mouza'),
                'class'       => 'PorchaProcessingBundle:Mouza',
                'property'    => 'name',
                'placeholder' => 'মৌজা নির্বাচন করুন'
            ))
            ->add('leases', 'collection', array(
                'type' => new GadgetLeaseInfo(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => 0
            ))
        ;


        $formModifier = function (FormInterface $form, Mouza $mouza = null) {
            $form->add('gadgetDetails', 'collection', array(
                'type' => new GadgetDetailsType($mouza),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getMouza());
            }
        );

        $builder->get('mouza')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent(), $event->getForm()->getData());
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\Gadget'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_gadget';
    }
}
