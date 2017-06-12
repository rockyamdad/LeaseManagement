<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkflowActionForm extends AbstractType
{
    private $entitled;

    public function __construct($entitled = false)
    {
        $this->entitled = $entitled;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->entitled) :
            $builder->add(
                'back',
                'button',
                array(
                    'label' => 'Correction Required',
                    'attr'  => array('class' => 'action btn red', 'data-action' => 'ACTION_BACK'),
                )
            );
            $builder
                ->add('correctionMessages', 'collection', array(
                        'type' => new KhatianCorrectionLogType()
                    )
                )
            ;
        endif;

        $builder->add(
            'forward',
            'button',
            array(
                'attr' => array('class' => 'action btn green', 'data-action' => 'ACTION_FORWARD'),
            )
        );

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