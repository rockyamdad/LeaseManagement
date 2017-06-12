<?php

namespace UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

class AssignUserToOfficeForm extends AbstractType
{
    /** @var User */
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;

        $builder->add('office', 'entity', array(
            'class' => 'AppBundle:Office',
            'property' => 'name',
            'required'    => false,
            'placeholder' => 'Please select',
            'empty_data'  => null,
            'query_builder' => function(EntityRepository $er) use ($user) {
                $officeType = $user->getType();

                $qb = $er->createQueryBuilder('o');
                $qb->where('o.type = :type')->setParameter('type', $officeType);
                $qb->andWhere('o.active = :active')->setParameter('active', 1);

                return $qb;
            }

        ));
        $builder
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_bundle_assign_user_to_office';
    }
}
