<?php

namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WaterBodyDetailsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('khatianDagNo','integer')
            ->add('totalAmount','integer')
            ->add('proposedAmount','integer')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\WaterBodyDetails'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_waterbodydetails';
    }
}
