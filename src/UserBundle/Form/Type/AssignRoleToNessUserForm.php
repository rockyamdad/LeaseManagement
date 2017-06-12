<?php

namespace UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

class AssignRoleToNessUserForm extends AbstractType
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

        $builder->add('groups', 'entity', array(
            'class' => 'UserBundle\Entity\Group',
            'property' => 'name',
            'required'    => false,
            'placeholder' => 'Please select',
            'empty_data'  => null,
            'query_builder' => function(EntityRepository $groupRepository) use ($user) {
                $qb = $groupRepository->createQueryBuilder('g')
                    ->andWhere("g.name != :group")->setParameter('group', 'Super Administrator');
                if ($user && $user->getOffice()) {
                    $qb->andWhere('g.applicableTo = :applicateTo')->setParameter('applicateTo', $user->getOffice()->getType());
                }

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
