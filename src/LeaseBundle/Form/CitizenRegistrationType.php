<?php
namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class CitizenRegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile',new CitizenProfileType())
            ->add('email', null, array(
                'constraints' => array(
                    new Assert\Email(array(
                        'message' => '{{ value }} এই ইমেলটি বৈধ ইমেল নয়.',
                        'checkMX' => false
                    )),
                   )))
            ->add('phoneNumber', null, array(
                'required'=> true,
                'constraints' => array(
                    new NotBlank(array('message'=>'তথ্য টি অবশ্যক'))
                ),
            ))
            ->add('nationalId', null, array(
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'পাসওয়ার্ড মেলেনি',
                'constraints' => array(
                    new Length(array('min' => 6)),
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'অ্যাকাউন্ট তৈরি করুন',
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
        return 'user_registration';
    }
}
