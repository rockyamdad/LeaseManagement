<?php

namespace PorchaProcessingBundle\Form;

use PorchaProcessingBundle\EventListener\AddMouzasFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class VolumeMouzaType extends AbstractType
{



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('startKhatianNo')
            ->add('endKhatianNo')
            ->add('id', 'hidden');


        $builder
            ->addEventSubscriber(new AddMouzasFieldSubscriber())
        ;


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\VolumeMouzas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_volume_mouzas';
    }
}