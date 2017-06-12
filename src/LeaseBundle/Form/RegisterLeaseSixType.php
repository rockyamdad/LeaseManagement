<?php

namespace LeaseBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterLeaseSixType extends AbstractType
{
    private $chalanAmount;
    private $submittedChallanAmount;

    public function __construct($chalanAmount = null, $submittedChallanAmount = null)
    {
        if($chalanAmount){

            $this->chalanAmount = array_filter($chalanAmount);
        }
        $this->submittedChallanAmount = $submittedChallanAmount;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('chalanNo','text',array(
                'required' => true,
                'attr' => array('class' => 'form-control input-large')
                ))
            ->add('chalanDate','date',array(
                'widget'=>'single_text',
                'required'=>true,
                'html5'=>false
            ))
            ->add('chalanAmount', 'choice', array(
                'required'=>false,
                'choices'     =>  !empty($this->chalanAmount) ? array_combine($this->chalanAmount,$this->chalanAmount) : array($this->submittedChallanAmount => $this->submittedChallanAmount),
                'attr' => array('class' => 'form-control select2 challan input-large'),
            ))

            ->add('nothiNo','integer',array(
                'required' => true,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('bank','text',array(
                'required' => true,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('branch','text',array(
                'required' => true,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('remarks','textarea',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\RegisterLeaseSix'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_registerleasesix';
    }
}
