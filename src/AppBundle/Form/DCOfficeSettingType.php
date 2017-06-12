<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DCOfficeSettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unionCount', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_number', 'maxlength' => 5),
                )
            )
            ->add('mouzaCount', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_number', 'maxlength' => 5),
                )
            )
            ->add('totalRecordCount', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_number', 'maxlength' => 10),
                )
            )
            ->add('uiscDeliveryFee', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_decimal', 'maxlength' => 6),
                )
            )
            ->add('uiscApplicationReceiveFee', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_decimal', 'maxlength' => 6),
                )
            )
            ->add('uiscDeliveryDay', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_number', 'maxlength' => 2),
                )
            )
            ->add('officePostalAddress', 'textarea', array(
                    'attr' => array('class' => 'form-control'),
                )
            )
            ->add('workflowTeam', 'checkbox', array(
                    'attr' => array('class' => 'form-control icheck'),
                    'required' => false,
                    'label' => ' '
                )
            )
            ->add('applicationLimitADay', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_number', 'maxlength' => 2),
                )
            )
            ->add('postFeeInDistrict', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_decimal', 'maxlength' => 6),
                )
            )
            ->add('postFeeOutDistrict', 'text', array(
                    'attr' => array('class' => 'form-control mask_en_decimal', 'maxlength' => 6),
                )
            )
            ->add('applicationReceiveMedia', 'choice', array(
                'attr' => array('class' => 'checkbox-list'),
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
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OfficeSettings'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dc_office_setting';
    }
}