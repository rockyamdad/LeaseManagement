<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GadgetLeaseInfo extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fiscalyear','choice',array(
                'required'=>true,
                'placeholder' => 'Year',
                'choices' => array_combine(range(1980,2060),range(1980,2060)),
                'attr' => array('class' => 'form-control select2 input-large'),
            ))
            ->add('applications', 'collection', array(
                'type' => new GadgetApplicationDetailsType(),
                'allow_add' => true,
                'allow_delete' => false,
                'prototype' => true,
                'prototype_name' => 0
            ))
        ;
           

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\Lease'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_lease_info';
    }
}
