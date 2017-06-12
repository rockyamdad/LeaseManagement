<?php

namespace AppBundle\Form;

use AppBundle\Entity\Office;
use AppBundle\Entity\Upozila;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ACLandOfficeType extends AbstractType
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
            ->add('parent', 'entity', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'property' => 'officeDistrictName',
                'class' => 'AppBundle:Office',
                'empty_value'=>'Select',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('o');
                    $qb->where("o.type = 'DC' ");
                    return $qb->andWhere("o.active = 1 ");
                },
            ))
            ->add('contactPerson', 'text', array(
                'attr' => array('class' => 'form-control input-large'),
                'required' => false
            ))
            ->add('contactInfo', 'textarea', array(
                'attr' => array('class' => 'form-control input-large'),
                'required' => false
            ))
            ->add('recordRoomInChargeInfo', 'textarea', array(
                'attr' => array('class' => 'form-control input-large'),
                'required' => false
            ));

            $formModifier = function (FormInterface $form, Office $office = null) {
                
                $form->add('upozila', 'entity', array(
                    'property' => 'name',
                    'attr' => array('class' => 'select2 form-control input-medium'),
                    'class'       => 'AppBundle:Upozila',
                    'placeholder' => 'Select',
                    'query_builder' => function (EntityRepository $er) use ($office) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where('u.district = :district');
                        $qb->setParameter('district', ($office) ? $office->getDistrict() : '0');
                        return $qb;
                    },
                ));
            };

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier) {

                    $data = $event->getData();
                    $formModifier($event->getForm(), $data ? $data->getParent() : null);
                }
            );

            $builder->get('parent')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier) {

                    $formData = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $formData);
                }
            );

            $builder->add('active', 'checkbox', array(
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
            'data_class' => 'AppBundle\Entity\Office'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_office';
    }
}