<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AcLandDocumentType extends AbstractType
{

    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file',array(
                'multiple'=>true
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ACLandDocument'
        ));*/
        $resolver->setDefaults(array(
            'data_class'=>'AppBundle\Entity\ACLandDocument',
            'csrf_protection'=>true,
            'csrf_field_name'=>'_token',
            'intention'=>'file'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aclanddocuments';
    }
}
