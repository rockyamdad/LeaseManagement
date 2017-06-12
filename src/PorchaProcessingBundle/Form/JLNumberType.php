<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class JLNumberType extends AbstractType
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
                    'constraints' => new NotBlank(),
                )
            )
            ->add('mouza', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'PorchaProcessingBundle\Entity\Mouza',
                    'placeholder' => 'Select',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('m');
                        $qb->where('m.deleted = 0');
                        return $qb;
                    },
                    'constraints' => new NotBlank(),
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
            'data_class' => 'PorchaProcessingBundle\Entity\JLNumber'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_jlnumber';
    }
}