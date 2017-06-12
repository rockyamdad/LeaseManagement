<?php
namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class CitizenPasswordChangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Password', 'password', array(
                'constraints' => array(
                    new Length(array('min' => 6)),
                    new NotBlank(array(
                            'message'=>'তথ্য টি অবশ্যক'

                        )
                    ))
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'পাসওয়ার্ড মেলেনি',
                'constraints' => array(
                    new Length(array('min' => 6)),
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'পরিবর্তন',
            ));

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LeaseBundle\Entity\CitizenUser',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_password_change';
    }
}
