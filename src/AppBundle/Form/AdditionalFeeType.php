<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdditionalFeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('applicationType', 'choice', array(
                'required'    => true,
                'attr' => array('class' => 'form-control select2 input-medium application_type'),
                'choices'  => array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map'),
            ))
            ->add('survey', 'entity', array(
                    'required'    => false,
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'PorchaProcessingBundle\Entity\Survey',
                    'placeholder' => 'Select',
                )
            )
            ->add('feeTypeKey1', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-medium'),
            ))
            ->add('feeTypeKey2', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-medium'),
            ))
            ->add('feeTypeKey3', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-medium'),
            ))
            ->add('feeTypeValue1', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-xsmall mask_en_decimal', 'maxlength' => 6),
            ))
            ->add('feeTypeValue2', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-xsmall mask_en_decimal', 'maxlength' => 6),
            ))
            ->add('feeTypeValue3', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-xsmall mask_en_decimal', 'maxlength' => 6),
            ))
            ->add('feeApplicable1', 'choice', array(
                'attr' => array('class' => 'checkbox-list additional_fee'),
                'choices' => array(
                    'DIRECT' => 'Direct',
                    'WEB' => 'WEB',
                    'UDC' => 'UDC'
                ),
                'required'    => false,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
            ->add('feeApplicable2', 'choice', array(
                'attr' => array('class' => 'checkbox-list additional_fee'),
                'choices' => array(
                    'DIRECT' => 'Direct',
                    'WEB' => 'WEB',
                    'UDC' => 'UDC'
                ),
                'required'    => false,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
            ->add('feeApplicable3', 'choice', array(
                'attr' => array('class' => 'checkbox-list additional_fee'),
                'choices' => array(
                    'DIRECT' => 'Direct',
                    'WEB' => 'WEB',
                    'UDC' => 'UDC'
                ),
                'required'    => false,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
            ->add('active', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-small'),
                'choices'  => array('0' => 'Disable', '1' => 'Enable'),
            ))
            ->add('locked', 'checkbox', array(
                    'label'    => 'Locked',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AdditionalFee'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'additional_fee';
    }
}