<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MarketDetailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('khatianDagNo','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('market', 'entity', array(
                'required'=>true,
                'attr' => array('class' => 'form-control select2 input-large market'),
                'class'       => 'LeaseBundle:Market',
                'property'    => 'marketName',
                'empty_value' => 'নির্বাচন করুন'
            ))
            ->add('shopNo','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large'),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\MarketDetail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_marketdetail';
    }
}
