<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VolumeIndexDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('halDagNo', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => true
                )
            )
            ->add('khatianNo', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => true
                )
            )
            ->add('sabekDagNo', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => false
                )
            )
            ->add('landQuantityAcre', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => false
                )
            )
            ->add('landQuantityDecimal', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => false
                )
            )
            ->add('comment', 'text', array(
                    'attr' => array('class' => 'form-control input-small'),
                    'required' => false
                )
            )
            ->add('remove', 'button', array(
                    'attr' => array('class' => 'remove')
                )
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\VolumeIndex'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'index_data';
    }
}