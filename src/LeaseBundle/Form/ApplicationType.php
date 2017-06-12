<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ApplicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('applicationDetails','textarea',array(
                'required'=>false,
                'attr' => array('class' => 'wysihtml5','rows'=> '6'),
            ))
            ->add('phoneNo',null,array(
                'required'=> true,
                'constraints' => array(
                    new NotBlank(array('message'=>'তথ্য টি অবশ্যক'))
                ),
            ))
            ->add('applicant', new ApplicantDetailsType()
            )
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
        return 'leasebundle_application';
    }
}
