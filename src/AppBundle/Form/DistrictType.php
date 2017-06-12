<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DistrictType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'constraints' => new NotBlank()
                )
            )
            ->add('geocode', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'constraints' => new NotBlank()
                )
            )
            ->add('division', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'AppBundle\Entity\Division',
                    'placeholder' => 'Select',
                )
            )
            ->add("approved", 'checkbox', array(
                    'attr' => array('class' => 'icheck'),
                    'required' => false
                )
            )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\District'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_district';
    }
}