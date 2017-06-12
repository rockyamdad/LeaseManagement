<?php

namespace PorchaProcessingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TemplateType extends AbstractType
{
    private $allApprove = false;

    /**
     * TemplateType constructor.
     */
    public function __construct($allowApprove)
    {
        $this->allApprove = $allowApprove;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array('class' => 'form-control'),
                'constraints' => new NotBlank()
            ))
            ->add('survey', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'PorchaProcessingBundle\Entity\Survey',
                    'placeholder' => 'Select',
                )
            )
            ->add('type', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'choices'  => array('PAGE1' => 'প্রথম পাতা ', 'PAGE1_ADDITIONAL' => 'প্রথম পাতার ইজা পাতা ', 'PAGE2' => 'দ্বিতীয় পাতা ', 'PAGE2_ADDITIONAL' => 'দ্বিতীয় পাতার ইজা পাতা '),
            ))
            ->add('body', 'textarea', array(
                'attr' => array('class' => 'form-control'),
                'required' => false
            ));

        if ($this->allApprove) {
            $builder->add('approved', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-small'),
                'choices'  => array('0' => 'No', '1' => 'Yes'),
            ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\Template'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template_bundle_template';
    }
}