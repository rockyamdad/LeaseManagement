<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GadgetLeaseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate','date',array(
                'widget'=>'single_text',
                'required'=>true,
                'html5'=>false
            ))
            ->add('endDate','date',array(
                'widget'=>'single_text',
                'required'=>true,
                'html5'=>false
            ))
           ;

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
