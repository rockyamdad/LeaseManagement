<?php

namespace PorchaProcessingBundle\Form;

use AppBundle\Form\IndexDataType;
use PorchaProcessingBundle\Entity\Volume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class VolumeIndexType extends AbstractType
{
    protected $volume;

    public function __construct(Volume $volume) {

        $this->volume = $volume;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('indexes', 'collection', array(
                'type' => new VolumeIndexDataType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'label_attr' => array(
                    'class' => 'hidden'
                )
            ))

            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\Volume'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_volume_index';
    }
}