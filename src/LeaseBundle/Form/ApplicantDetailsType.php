<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ApplicantDetailsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('fatherName','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('motherName','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('spouseName','text',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('dob','date',array(
                'widget'=>'single_text',
                'required'=>true,
                'html5'=>false
            ))
            ->add('nid','text',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit', 'pattern'=>'^(\d{13}|\d{17})$'),
            ))
            ->add('email','email',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('address','textarea',array(
                'required'=>true,
                'attr' => array('class' => 'form-control input-large bn-digit'),
            ))
            ->add('gender', 'choice', array(
                'required'=>true,
                'choices'     => array(
                    'Male'  => 'পুরুষ ',
                    'Female'  => 'মহিলা',
                    'Other'  => 'অন্যান্য',
                ),
                'attr' => array('class' => 'form-control select2 input-large'),
                'empty_data'  => '',
                'empty_value' => 'লিংগ নির্বাচন',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\Applicant'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_applicantdetails';
    }
}
