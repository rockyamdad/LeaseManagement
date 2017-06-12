<?php
namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class CitizenLoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', null, array(
                'required'=> true,
                'constraints' => array(
                    new NotBlank(array('message'=>'তথ্য টি অবশ্যক'))
                ),
            ))
            ->add('password', 'password', array(
                'constraints' => array(
                new Length(array('min' => 6)),
                new NotBlank(array(
                        'message'=>'তথ্য টি অবশ্যক'

                    )
                ))
            ))
            ->add('save', 'submit', array(
                'label' => 'লগইন',
            ));

        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_login';
    }
}
