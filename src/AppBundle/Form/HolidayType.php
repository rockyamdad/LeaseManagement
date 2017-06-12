<?php

namespace AppBundle\Form;

use AppBundle\Entity\Holiday;
use AppBundle\Entity\Upozila;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class HolidayType extends AbstractType
{
    private $year;
    protected $officeType;

    public function __construct($officeType)
    {
        $this->officeType = $officeType;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = array();
        $endDate = date( 'Y') + 10;
        for($i=date("Y");$i<$endDate;$i++) {
                        $year[$i] = $i;
                    }
        $this->year = $year;

        $holidayType = array('WEEK_LEAVE' => 'Week Leave', 'GOV_LEAVE' => 'Govt Leave', 'CEO_LEAVE' => 'CEO Leave');
        if($this->officeType != 'MINISTRY') {
            unset($holidayType['WEEK_LEAVE']);
        }

        $builder
            ->add('type', 'choice', array(
                'required'    => true,
                'attr' => array('class' => 'form-control select2 input-large'),
                'choices'  => $holidayType,
            ))
            ->add('year', 'choice', array(
                'mapped'=>false,
                'attr' => array('class' => 'form-control select2 input-large'),
                'choices'  => $year,
            ))
            ->add('day', 'choice', array(
                'mapped'=>false,
                'attr' => array('class' => 'checkbox-list'),
                'choices' => array('friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday',
                    'monday' => 'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday',' thursday'=>'Thursday'),
                'required'    => false,
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
            ))
            ->add('title', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('startDate', 'text', array(
                'required'    => false,
                'mapped'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('endDate', 'text', array(
                'required'    => false,
                'mapped'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Holiday'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_holiday';
    }
}