<?php

namespace PorchaProcessingBundle\Form;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\EventListener\AddMouzaFieldSubscriber;
use PorchaProcessingBundle\EventListener\AddUpozilaFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MouzaMapRequestPorchaType extends AbstractType
{
    protected $districtIds;

    public function __construct($districtIds)
    {
        $this->districtIds = $districtIds;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $districtIds = $this->districtIds;
        $builder
            ->add('survey', null, array(
                'attr' => array(
                    'class' => 'survey_selector'
                ),
                'required' => true
            ))
            ->add('district', null, array(
                'property' => 'name',
                'empty_value' => 'Select',
                'attr' => array(
                    'class' => 'district_selector',
                ),
                'required' => true,
                'class' => 'PorchaProcessingBundle\Entity\District',
                'placeholder' => 'Select',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($districtIds) {
                    $qb = $er->createQueryBuilder('d');
                    $qb->where('d.deleted = 0');
                    $qb->andWhere('d.approved = 1');

                    if (!empty($districtIds)) {
                        $qb->andwhere('d.id IN (:id)');
                        $qb->setParameter('id', array_values($districtIds));
                    }

                    return $qb;
                },
            ))
            ->add('jlNo', null, array('attr' => array('class' => 'jl_selector')))
            ->add('sheetNo', null, array('required' => true))
            ->add('dagNo')
        ;

        $builder
            ->addEventSubscriber(new AddUpozilaFieldSubscriber())
            ->addEventSubscriber(new AddMouzaFieldSubscriber())
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\ServiceRequestPorcha'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porchaprocessingbundle_servicerequestporcha';
    }
}
