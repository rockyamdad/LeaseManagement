<?php

namespace PorchaProcessingBundle\Form\Report;

use PorchaProcessingBundle\EventListener\AddMouzaFieldSubscriber;
use PorchaProcessingBundle\EventListener\AddUpozilaFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UdcReceivedReportSearchType extends AbstractType
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
            ->add('start_date', 'text',array(
                'attr' => array(
                    'placeholder' => 'Start Date'
                ),
                'read_only' => true
            ))
            ->add('end_date', 'text',array(
                'attr' => array(
                    'placeholder' => 'End Date'
                ),
                'read_only' => true
            ))
            ->add('application_type', 'choice', array(
                'choices' => array(
                    'PORCHA_REQUEST' => 'পর্চা বাবস্থাপনা',
                    'MOUZA_MAP' => 'মৌজা  ম্যাপ',
                    'CASE_COPY' => 'কেস কপি ',
                    'INFORMATION_SLIP' => 'তথ্য স্লিপ',
                ),
                'multiple' => false,
                'required' => true
            ))
            ->add('district', 'entity', array(
                'class' => 'PorchaProcessingBundle\Entity\District',
                'property' => 'name',
                'empty_value' => 'Select',
                'attr' => array(
                    'class' => 'district_selector',
                ),
                'required' => true,
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
            ));

        $builder

         //   ->addEventSubscriber(new AddUpozilaFieldSubscriber())
          //  ->addEventSubscriber(new AddMouzaFieldSubscriber())
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'search';
    }
}
