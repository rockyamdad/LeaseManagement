<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UdcUsersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = isset($options['data']) ? $options['data'] : null;

        $builder
            ->add('username', 'text', array(
                'attr' => array('class' => 'form-control input-small'),
            ));

        $passwordConstrain = $data && $data->getId() ? array() : array(
            new NotBlank(
                array(
                    'message' => 'Password should not be blank',
                )
            ),
            new Length(array('min' => 6)),
        );

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
                'required'        => $data && $data->getId()
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'udc_users';
    }
}