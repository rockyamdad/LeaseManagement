<?php

namespace PorchaProcessingBundle\Form;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\Entity\Upozila;
use PorchaProcessingBundle\Entity\WorkflowTeam;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class WorkflowTeamType extends AbstractType
{
    private $office;
    private $relatedDistrictIds;

    public function __construct( $office = null, $relatedDistrictIds = null)
    {
        $this->office = $office;
        $this->relatedDistrictIds = $relatedDistrictIds;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'constraints' => new NotBlank()
            ))
            ->add('surveys', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2'),
                    'class' => 'PorchaProcessingBundle\Entity\Survey',
                    'multiple' => true
                )
            )
            ->add('upozilas', 'entity',
                array(
                    'attr' => array('class' => 'form-control select2'),
                    'class' => 'PorchaProcessingBundle\Entity\Upozila',
                    'multiple' => true,
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where('u.deleted = 0');
                        $qb->andWhere('u.approved = 1');

                        if (!empty($this->relatedDistrictIds)) {
                            $qb->andwhere('u.district IN (:id)');
                            $qb->setParameter('id', array_values($this->relatedDistrictIds));
                        }

                        return $qb;
                    },

                )
            );

            $formModifier = function (FormInterface $form, $upozilas = null) {

                $upzs = array();
                if ($upozilas) {
                    foreach ($upozilas as $uz) {
                        $upzs[] = $uz;
                    }
                }

                $form->add('mouzas', 'entity', array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2'),
                    'class'       => 'PorchaProcessingBundle\Entity\Mouza',
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) use ($upzs) {
                        $qb = $er->createQueryBuilder('m');
                        $qb->where('m.upozila IN (:upozilas)');
                        $qb->setParameter('upozilas', ($upzs) ? $upzs : array());

                        return $qb;
                    },
                    'required' => false
                ));
            };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                /** @var WorkflowTeam $data */
                $data = $event->getData();
                $formModifier($event->getForm(), $data ? $data->getUpozilas() : null);
            }
        );

        $builder->get('upozilas')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {

                $formData = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $formData);
            }
        );

        $builder
            ->add('entryOperators', 'entity', $this->entityProperty('%ROLE_KHATIAN_ENTRY%'))
            ->add('verifiers', 'entity', $this->entityProperty('%ROLE_KHATIAN_VERIFICATION%'))
            ->add('comparers', 'entity', $this->entityProperty('%ROLE_KHATIAN_COMPARISON%'))
            ->add('approvers', 'entity', $this->entityProperty('%ROLE_KHATIAN_APPROVAL%'))
            ;
        ;
    }

    private function entityProperty($roleSearch) {

        return array(
            'property' => 'nameWithUsername',
            'attr' => array('class' => 'form-control select2'),
            'class' => 'UserBundle\Entity\User',
            'multiple' => true,
            'required' => true,
            'constraints' => new Count(array('min' => 1, 'minMessage' => 'atleast one person required')),
            'query_builder' => function (EntityRepository $er) use ($roleSearch) {
                $qb = $er->createQueryBuilder('u');
                $qb->join('u.profile', 'p');
                $qb->join('u.groups', 'g');
                $qb->where('u.office = :office')->setParameter('office', $this->office);
                $qb->andWhere('g.roles LIKE :role')->setParameter('role', $roleSearch);
                $qb->andWhere('u.enabled = :enabled')->setParameter('enabled', true);
                return $qb;
            },
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\WorkflowTeam'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_workflow_team';
    }
}