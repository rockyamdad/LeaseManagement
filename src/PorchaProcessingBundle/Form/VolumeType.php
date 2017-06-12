<?php

namespace PorchaProcessingBundle\Form;

use PorchaProcessingBundle\EventListener\AddUpozilaFieldSubscriber;
use PorchaProcessingBundle\Validator\Constraints\VolumeExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\User;

class VolumeType extends AbstractType
{
    protected $user;
    protected $relatedDistricts;
    protected $volume;
    protected $permissions;

    public function __construct(User $user, $volume = null, $permissions = array()) {

        $this->user = $user;
        $this->relatedDistricts = $user->getOffice()->getRelatedDistricts();
        $this->volume = $volume;
        $this->permissions = $permissions;
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
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('s');
                        return $qb;
                    },
                    'constraints' => new NotBlank(),
                    'required' => true,
                    'disabled' => ($this->volume->getId()) ? true : false
                )
            );

        $builder->add('district', 'entity',
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
                    },
                    'constraints' => new NotBlank(),
                    'required' => true,
                    'disabled' => ($this->volume->isApproved()) ? true : false
                )
            )
            ->add('pargana', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false,
                    'disabled' => ($this->volume->isApproved()) ? true : false
                )
            )

            ->add('subKhatianNos', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false,
                    'disabled' => ($this->volume->isApproved()) ? true : false
                )
            )
            ->add('missingKhatianNos', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'required' => false,
                    'disabled' => ($this->volume->isApproved()) ? true : false
                )
            );

        if (!$this->volume->getId()) {
            $builder->add('volumeNo', 'text', array(
                    'attr' => array('class' => 'form-control input-large bn-digit', 'maxlength' => '5'),
                    'constraints' => array(
                        new NotBlank(),
//                        new VolumeExists()
                    ),
                    'required' => true,
                )
            );
        }

        if (!empty($this->permissions)) {
            if (array_key_exists('ROLE_VOLUME_BOOK_APPROVED', $this->permissions) && $this->permissions['ROLE_VOLUME_BOOK_APPROVED']) {
                $builder->add(
                    'approved', 'checkbox', array(
                        'label'    => 'Approved',
                        'attr'     => array('class' => 'icheck'),
                        'required' => false,
                    )
                );
            }
        }

        $builder
            ->addEventSubscriber(new AddUpozilaFieldSubscriber($this->volume->isApproved()));

        $builder->add('volumeMouzas', 'collection', array(
            'type' => new VolumeMouzaType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'disabled' => ($this->volume->isApproved()) ? true : false
        ));
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