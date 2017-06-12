<?php

namespace UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UdcUserForm extends AbstractType
{
    /** @var User */
    protected $loginUser = null;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    public function __construct($user, AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->authorizationChecker = $authorizationCheckerInterface;
    }

    public function setLoginUser(User $user)
    {
        $this->loginUser = $user;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $loginUser = $this->loginUser;
        $builder->add('profile', new UdcProfileForm());
        /** @var User $data */
        $data = $options['data'];

        if ($data->getId() === null) {

            $builder
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle',
                      'constraints' => array(
                          new NotBlank(array(
                              'message'=>'Username should not be blank'
                          ))
                      ),
                ));

            $builder->add('email', 'email', array(
                'label' => 'form.email', 'translation_domain' => 'FOSUserBundle',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Email should not be blank'
                    )),
                    new email()
                ),
            ));
        }

        $passwordConstrain = $data->getId() ? array() : array(
            new NotBlank(
                array(
                    'message' => 'Password should not be blank',
                )
            ),
            new Length(array('min' => 6)),
        );

        if ($data->getId() === null || $data->getId() == $this->loginUser->getId() || $this->authorizationChecker->isGranted('ROLE_CHANGE_USER_PASSWORD')) {
            $builder->add(
                'plainPassword',
                'repeated',
                array(
                    'type'            => 'password',
                    'options'         => array('translation_domain' => 'FOSUserBundle'),
                    'first_options'   => array('label' => 'form.password'),
                    'second_options'  => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                    'constraints'     => $passwordConstrain,
                    'required'        => !$data->getId()
                )
            );
        }




        $builder->add('submit', 'submit', array(
            'attr'     => array('class' => 'btn green')
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
