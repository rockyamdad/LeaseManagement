<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UdcEntrepreneurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('fatherName', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('educationalQualification', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('mobileNo', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('email', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('address', 'textarea', array(
                'attr' => array('class' => 'form-control input-small'),
                'label' => false
            ))
            ->add('gender', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-small'),
                'label' => false,
                'choices'  => array(
                    'MALE' => 'পুরুষ',
                    'FEMALE' => 'নারী',
                    'OTHER' => 'অন্যান্য',
                ),
            ))
            ->add('entrepreneurType', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-small'),
                'label' => false,
                'choices'  => array(
                    'entrepreneur_uddokthta' => 'উদ্যোক্তা',
                    'education_entrepreneur' => 'শিক্ষানবিস উদ্যোক্তা'
                ),
            ));
        $builder->add('remove', 'button')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UdcEntrepreneur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'udc_entrepreneur';
    }
}