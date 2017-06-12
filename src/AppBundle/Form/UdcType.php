<?php

namespace AppBundle\Form;

use AppBundle\Entity\District;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use UserBundle\Entity\User;

class UdcType extends AbstractType
{

    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('udcName', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('udcMobileNo', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('udcEmail', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('udcAddress', 'textarea', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('usName', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('usMobileNo', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('usEmail', 'text', array(
                'required'    => false,
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('usAddress', 'textarea', array(
                'attr' => array('class' => 'form-control input-small'),
            ))
            ->add('udcEntrepreneurs', 'collection', array(
                'type' => new UdcEntrepreneurType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'label_attr' => array(
                    'class' => 'hidden'
                )
            ));
//            ->add('user', new UdcUsersType())
            if($this->status == 'update'){

                $builder->add('status', 'choice', array(
                    'attr' => array('class' => 'form-control select2 input-small'),
                    'choices'  => array(
                        'WAITING_FOR_APPROVAL' => 'WAITING FOR APPROVAL',
                        'APPROVED' => 'APPROVED',
                        'CANCELED' => 'CANCELED',
                    ),
                ));
            }

        $builder->add('district', 'entity', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'property' => 'name',
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

            $form->add('union', 'entity', array(
                'property' => 'name',
                'attr' => array('class' => 'select2 form-control input-medium'),
                'class'       => 'AppBundle:Union',
                'placeholder' => 'Select',
                'query_builder' => function (EntityRepository $er) use ($district) {
                    $qb = $er->createQueryBuilder('un');
                    $qb->join('un.upozila','up');
                    $qb->join('up.district','d');
                    $qb->where('d.id = :district');
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

      /*  $builder->get('district')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {

                $formData = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $formData);
            }
        );*/

           /* ->add('office', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'class' => 'AppBundle\Entity\Office',
                    'placeholder' => 'Select Office',
                )
            )*/
//        $builder->add('submit', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Udc'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'udc';
    }
}