<?php
namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class CitizenProfileEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profile',new ProfileType())
            ->add('email', null, array(
                'constraints' => array(
                    new Assert\Email(array(
                        'message' => '{{ value }} এই ইমেলটি বৈধ ইমেল নয়.',
                        'checkMX' => false
                    )),
                   )))
            ->add('nationalId', null, array(
            ))
            ->add('save', 'submit', array(
                'label' => 'দাখিল করুন',
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
