<?php

namespace LeaseBundle\Form;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Mouza;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GadgetDetailsType extends AbstractType
{
    /**
     * @var Mouza
     */
    private $mouza;

    /**
     * GadgetDetailsType constructor.
     */
    public function __construct(Mouza $mouza = null)
    {
        $this->mouza = $mouza;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $_this = $this;

        $builder
            ->add('saKhatianNo','entity',array(
                'required'=>false,
                'attr' => array('class' => 'form-control select2'),
                'class'       => 'PorchaProcessingBundle:Khatian',
                'property'    => 'khatianNo',
                'empty_value' => 'খতিয়ান নির্বাচন করুন',
                'query_builder' => function (EntityRepository $er) use ($_this) {
                    return $_this->buildKhatianQueryBuilderForMouzaAndSurveyType($er, 'SA');
                }
            ))
            ->add('saDagNo','integer',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('rsKhatianNo','entity',array(
                'required'=>true,
                'attr' => array('class' => 'form-control select2'),
                'class'       => 'PorchaProcessingBundle:Khatian',
                'property'    => 'khatianNo',
                'empty_value' => 'খতিয়ান নির্বাচন করুন',
                'query_builder' => function (EntityRepository $er) use ($_this) {
                    return $_this->buildKhatianQueryBuilderForMouzaAndSurveyType($er, 'RS');
                }
            ))
            ->add('rsDagNo','integer',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('totalAmount','integer',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('proposedAmount','text',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('propertyType','text',array(
                'required'=>false,
                'attr' => array('class' => 'form-control input-large'),
            ))
        ;
    }

    /**
     * @param EntityRepository $er
     * @param $surveyType
     * @return \Doctrine\ORM\QueryBuilder
     */
    function buildKhatianQueryBuilderForMouzaAndSurveyType(EntityRepository $er, $surveyType)
    {
        $qb = $er->createQueryBuilder('k');
        $qb->join('k.mouza', 'm');
        $qb->join('k.jlnumber', 'j');
        $qb->where('k.mouza = :mouza');
        $qb->andWhere('j.surveyType = :survey');
        $qb->setParameter('mouza', $this->mouza);
        $qb->setParameter('survey', $surveyType);

        return $qb;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\GadgetDetails'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'leasebundle_gadgetdetails';
    }
}
