<?php

namespace PorchaProcessingBundle\Form;

use PorchaProcessingBundle\Validator\Constraints\BanglaDigitsOnly;
use PorchaProcessingBundle\Validator\Constraints\VolumeExists;
use PorchaProcessingBundle\Validator\Constraints\VolumeRangeChecking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\User;

class VolumeType extends AbstractType
{
    protected $user;
    protected $relatedDistricts;

    public function __construct(User $user) {

        $this->user = $user;
        $this->relatedDistricts = $user->getOffice()->getRelatedDistricts();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('survey', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-large'),
                    'class' => 'PorchaProcessingBundle\Entity\Survey',
                    'placeholder' => 'Select',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('s');
                        return $qb;
                    },
                    'constraints' => new NotBlank(),
                    'required' => false,
                )
            )
            ->add('district', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium mo-district'),
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
                    },
                    'constraints' => new NotBlank(),
                    'required' => false,
                )
            )
            ->add('upozila', 'text',
                array(
                    'attr' => array('class' => 'form-control input-large mo-upozila', 'placeholder'=> 'Select'),
                    'constraints' => new NotBlank(),
                    'required' => false,
                )
            )
            ->add('mouza', 'text',
                array(
                    'attr' => array('class' => 'form-control input-large mo-mouza', 'placeholder'=> 'Select'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'required' => false,
                )
            )
            ->add('pargana', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false,
                )
            )
            ->add('volumeNo', 'text', array(
                    'attr' => array('class' => 'form-control input-large bn-digit'),
                    'constraints' => array(
                        new NotBlank(),
                        new VolumeExists()
                    ),
                    'required' => false,
                )
            )
            ->add('startKhatianNo', 'text', array(
                    'attr' => array('class' => 'form-control input-large bn-digit'),
                    'required' => false,
                    'constraints' => array(
                        new NotBlank(),
                        new BanglaDigitsOnly()
                    )
                )
            )
            ->add('endKhatianNo', 'text', array(
                    'attr' => array('class' => 'form-control input-large bn-digit'),
                    'required' => false,
                    'constraints' => array(
                        new NotBlank(),
                        new BanglaDigitsOnly(),
                        new VolumeRangeChecking()
                    )
                )
            )
            ->add('noOfKhatians', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false,
                )
            )
            ->add('subKhatianNos', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false
                )
            )
            ->add('missingKhatianNos', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
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
            'data_class' => 'PorchaProcessingBundle\Entity\Volume'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_volume';
    }
}