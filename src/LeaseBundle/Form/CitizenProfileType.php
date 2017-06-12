<?php
namespace LeaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class CitizenProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('birthDate', null, array(
            ))
            ->add('file', 'file', array(
                'data_class' => null,
                'constraints' => array(
                new Assert\File(array(
                    'maxSize' => '1024k',
                    'mimeTypes' => array(
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/gif',
                    ),
                    'mimeTypesMessage' => 'একটি বৈধ চিত্র ফাইল আপলোড করুন',
                ))
                )))
            ->add('fullname', null, array(
                'constraints' => array(
                    new NotBlank(array(
                            'message'=>'তথ্য টি অবশ্যক'
                        ),
                        new Length(array('min' => 5))
                    ))
            ))
            ->add('fatherName', null, array(
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'তথ্য টি অবশ্যক'
                    ),
                        new Length(array('min' => 5))
                    ))
            ))
            ->add('motherName', null, array(
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'তথ্য টি অবশ্যক'
                    ),
                        new Length(array('min' => 5))
                    ))
            ))
            ->add('gender', 'choice', array(
                'choices'  => array(
                    'পুরুষ'=>'পুরুষ',
                    'মহিলা'=>'মহিলা',
                ),
                'multiple' => false,
                'constraints' => array(
                    new NotBlank(array('message'=>'তথ্য টি অবশ্যক'))
                ),
            ))

            ->add('address', null, array(
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'তথ্য টি অবশ্যক'
                    ),
                        new Length(array('min' => 5))
                    ))
            ))

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
        return 'user_profile';
    }
}
