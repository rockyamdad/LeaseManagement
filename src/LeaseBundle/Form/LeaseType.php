<?php

namespace LeaseBundle\Form;

use LeaseBundle\Entity\WaterBodyDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LeaseType extends AbstractType
{

    private $chalanAmount;
    private $submittedChallanAmount;
    private $current;

    public function __construct($chalanAmount = null, $submittedChallanAmount = null,$current = null)
    {
        if($chalanAmount){

            $this->chalanAmount = array_filter($chalanAmount);
        }
        $this->submittedChallanAmount = $submittedChallanAmount;
        $this->current = $current;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('khatian', 'entity', array(
                'required'=>true,
                'attr' => array('class' => 'form-control select2 input-large khatianList'),
                'class'       => 'PorchaProcessingBundle:Khatian',
                'property'    => 'khatianNo',
                'empty_value' => 'খতিয়ান নির্বাচন করুন'
            ))
            ->add('name','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('shotangso','number',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large'),
            ));

        if($this->current == 1){
            $builder->add('fiscalyear','choice',array(
                'required'=>true,
                'placeholder' => 'Year',
                'choices' => array_combine(range(2016,2060),range(2016,2060)),
                'attr' => array('class' => 'form-control select2 input-large'),
            ));
        }else{
            $builder->add('fiscalyear','choice',array(
                'required'=>true,
                'placeholder' => 'Year',
                'choices' => array_combine(range(1980,2060),range(1980,2060)),
                'attr' => array('class' => 'form-control select2 input-large'),
            ));
        }

        $builder->add('waterBodyDetails', 'collection', array(
                'type' => new WaterBodyDetailsType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ))
            ->add('marketDetail',new MarketDetailType())

            ->add('applications', 'collection', array(
                'type' => new ApplicationDetailsType($this->chalanAmount,$this->submittedChallanAmount),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => 0
            ))
            ->add('remarks','textarea',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ));

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
        return 'leasebundle_lease';
    }
}
