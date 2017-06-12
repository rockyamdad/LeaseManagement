<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationWorkflowActionForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'back',
            'button',
            array(
                'label' => 'Correction Required',
                'attr'  => array('class' => 'action btn-default', 'data-action' => 'ACTION_BACK'),
            )
        );
        $builder->add(
            'forward',
            'button',
            array(
                'attr' => array('class' => 'action btn-default', 'data-action' => 'ACTION_FORWARD'),
            )
        );
        $builder
            ->add('correctionMessages', 'collection', array(
                    'type' => new KhatianCorrectionLogType()
                )
            )
        ;
        $builder->add('workflowAction', 'hidden', array(
            'mapped' => null
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_workflow_action';
    }
}