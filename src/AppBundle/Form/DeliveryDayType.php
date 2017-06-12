<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryDayType extends AbstractType
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
            ->add('normalDeliveryHasEntry', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('normalDeliveryNotEntry', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('normalDeliveryNonDeliverable', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('emergencyDeliveryHasEntry', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('emergencyDeliveryNotEntry', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('emergencyDeliveryNonDeliverable', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small mask_en_number', 'maxlength' => 2),
            ))
            ->add('active', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'choices'  => array('0' => 'Disable', '1' => 'Enable'),
            ))
            ->add('locked', 'checkbox', array(
                    'label'    => 'Locked',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            );
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DeliveryDaySettings'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'delivery_day';
    }
}