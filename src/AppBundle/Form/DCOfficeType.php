<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DCOfficeType extends AbstractType
{
    protected $except;

    public function __construct($except = null) {

        $this->except = $except;
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
            ))
            ->add('district', 'entity', array(
                'attr' => array('class' => 'form-control select2 input-medium'),
                'property' => 'name',
                'class' => 'AppBundle:District',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('d');
                    /*$qb->leftJoin('d.offices', 'o');
                    $qb->where('d.approved = 1');
                    $qb->andWhere($qb->expr()->isNull('o.district'));
                    if ($this->except) {
                        $qb->orWhere('o.district = :except');
                        $qb->setParameter('except', $this->except);
                    }*/
                    return $qb;
                },
                'constraints' => new NotBlank()
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
            ))
            ->add('nessOrgId', 'text', array(
                'attr' => array('class' => 'form-control input-large'),
                'required' => false
            ))
            ->add('active', 'checkbox', array(
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