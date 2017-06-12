<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\User;

class ThanaType extends AbstractType
{
    protected $user;
    protected $relatedDistricts;
    protected $includeApproved;

    public function __construct(User $user, $includeApproved) {

        $this->user = $user;
        $this->relatedDistricts = $user->getOffice()->getRelatedDistricts();
        $this->includeApproved = $includeApproved;
    }

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
                    'class' => 'PorchaProcessingBundle\Entity\District',
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
            ->add("deleted", 'checkbox', array(
                    'label' => 'মুছে ফেলুন ',
                    'attr' => array('class' => 'icheck'),
                    'required' => false
                )
            );

        if ($this->includeApproved) {
            $builder->add(
                'approved', 'checkbox', array(
                    'label'    => 'Approved',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\Thana'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_thana';
    }
}