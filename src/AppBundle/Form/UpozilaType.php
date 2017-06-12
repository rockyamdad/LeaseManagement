<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\User;

class UpozilaType extends AbstractType
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
                    'constraints' => new NotBlank()
                )
            )
            ->add('geocode', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'constraints' => new NotBlank()
                )
            )
            ->add('district', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'AppBundle\Entity\District',
                    'placeholder' => 'Select',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('d');
                        $qb->where('d.deleted = 0');
                        $qb->andWhere('d.approved = 1');

                        if (!empty($this->relatedDistricts)) {
                            $relatedDistricts = explode(",", $this->relatedDistricts);
                            $qb->andwhere('d.geocode IN (:districtGeocode)');
                            $qb->setParameter('districtGeocode', array_values($relatedDistricts));
                        }

                        return $qb;
                    }
                )
            )
            ->add(
                'approved', 'checkbox', array(
                    'label'    => 'Approved',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            )
            ->add("deleted", 'checkbox', array(
                    'label' => 'মুছে ফেলুন ',
                    'attr' => array('class' => 'icheck'),
                    'required' => false
                )
            );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Upozila'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_upozila';
    }
}