<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LeaseStatusChangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'required'=>true,
                'choices'     => array(
                    'CLOSED'  => 'সক্রিয় করা',
                    'TERMINATED'  => 'বাতিল করা',
                    'SUSPEND'  => 'স্থগিত করা',
                ),
                'attr' => array('class' => 'form-control select2 input-large'),
                'empty_data'  => '',
                'empty_value' => 'প্রকার নির্বাচন করুন',
            ))
        ;
        

    }
    
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'lease_status_change';
    }
}
