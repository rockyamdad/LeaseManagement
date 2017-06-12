<?php

namespace AppBundle\Form;

use AppBundle\Entity\District;
use AppBundle\Entity\Union;
use AppBundle\Entity\Upozila;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UnionType extends AbstractType
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
            ))
            ->add('geocode', 'text', array(
                    'attr' => array('class' => 'form-control input-large'),
                    'constraints' => new NotBlank()
                )
            )
            ->add('district', 'entity', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'property' => 'name',
                'required' => false,
                'mapped'=>false,
                'class' => 'AppBundle:District',
                'empty_value'=>'Select',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('d');
                    return $qb->where("d.approved = 1 ");
                },
            ));

            $formModifier = function (FormInterface $form, District $district = null) {

                $form->add('upozila', 'entity', array(
                    'property' => 'name',
                    'attr' => array('class' => 'select2 form-control input-medium'),
                    'class'       => 'AppBundle:Upozila',
                    'placeholder' => 'Select',
                    'query_builder' => function (EntityRepository $er) use ($district) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where('u.district = :district');
                        $qb->setParameter('district', ($district) ? $district->getId() : '0');
                        return $qb;
                    },
                ));
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier) {

                    $data = $event->getData();
                    $formModifier($event->getForm(), $data && $data->getUpozila() ? $data->getUpozila()->getDistrict() : null);
                }
            );

            $builder->get('district')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier) {

                    $formData = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $formData);
                }
            );

            $builder->add('approved', 'checkbox', array(
                        'attr'     => array('class' => 'icheck'),
                        'required' => false,
                    )
            );
        $builder->add('deleted', 'checkbox', array(
                        'attr'     => array('class' => 'icheck'),
                        'required' => false,
                    )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Union'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_union';
    }
}