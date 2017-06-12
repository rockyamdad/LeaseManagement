<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationDetailsType extends AbstractType
{

    private $chalanAmount;
    private $submittedChallanAmount;

    public function __construct($chalanAmount = null, $submittedChallanAmount = null)
    {
        if($chalanAmount){

            $this->chalanAmount = array_filter($chalanAmount);
        }
        $this->submittedChallanAmount = $submittedChallanAmount;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNo')
            ->add('applicant',  new ApplicantDetailsType()
            )
            ->add('registerSix', 'collection', array(
                'type' => new RegisterLeaseSixType($this->chalanAmount,$this->submittedChallanAmount),
                'allow_add' => true,
                'allow_delete' => true,
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
            'data_class' => 'LeaseBundle\Entity\Application'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_applicationdetails';
    }
}
