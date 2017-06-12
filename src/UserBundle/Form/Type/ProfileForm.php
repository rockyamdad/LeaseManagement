<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullNameEn')
            ->add('fullNameBn')
            ->add('nid', 'text')
            ->add('designation')
            ->add('gender', 'choice', array(
                'choices' => array('Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('cellphone')
            ->add('photoFile', 'file', array(
                'required' => false,
                'attr' => array(
                    'accept' => "image/*"
                )
            ))
            ->add('signatureFile', 'file', array(
                'required' => false,
                'attr' => array(
                    'accept' => "image/*"
                )
            ))
        ;

        $builder->add('fathersFullNameBn');
        $builder->add('fathersFullNameEn');
        $builder->add('mothersFullNameBn');
        $builder->add('mothersFullNameEn');
        $builder->add('currentAddress');
        $builder->add('permanentAddress');
        $builder->add('brn', null, array(
            'required' => false
        ));
        $builder->add('dob', null, array(
            'widget' => 'single_text',
            'format' => 'd-M-y',
            'attr' => array(
                'class' => 'date-picker'
            )
        ));
        $builder->add('telephone', null, array(
            'required' => false
        ));
        $builder->add('joiningDate', null, array(
            'widget' => 'single_text',
            'format' => 'd-M-y',
            'attr' => array(
                'class' => 'date-picker'
            )
        ));
        $builder->add('joiningDateOfCurrentOffice', null, array(
            'widget' => 'single_text',
            'format' => 'd-M-y',
            'attr' => array(
                'class' => 'date-picker'
            )
        ));

        $builder->add('religion', 'choice', array(
            'choices' => $this->religionList(),
            'required'    => false,
            'placeholder' => 'Select',
            'empty_data'  => null,
        ));
        $builder->add('educationLevel', 'choice', array(
            'choices' => $this->educationLevelList(),
            'required'    => false,
            'placeholder' => 'Select',
            'empty_data'  => null,
        ));
        $builder->add('disability', 'choice', array(
            'choices' => $this->disabilityList(),
            'required'    => false,
            'placeholder' => 'Select',
            'empty_data'  => null,
        ));
        $builder->add('bloodGroup', 'choice', array(
            'choices' => $this->bloodGroupList(),
            'required'    => false,
            'placeholder' => 'Select',
            'empty_data'  => null,
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'user_profile';
    }

    private function religionList()
    {
        return array(
            'ইসলাম' => 'ইসলাম',
            'হিন্দু' => 'হিন্দু',
            'বৌদ্ধ' => 'বৌদ্ধ',
            'খ্রীস্টান' => 'খ্রীস্টান',
            'প্রকাশ করতে অস্বীকার' => 'প্রকাশ করতে অস্বীকার',
            'নাস্তিক' => 'নাস্তিক',
            'অন্যান্য' => 'অন্যান্য',
        );
    }

    private function educationLevelList()
    {
        return array(
            'এস এস সি' => 'এস এস সি',
            'এইচ এস সি' => 'এইচ এস সি',
            'বি এস সি' => 'বি এস সি',
            'বি এস সি(অনার্স)' => 'বি এস সি(অনার্স)',
            'বি এ' => 'বি এ',
            'বি এ(অনার্স)' => 'বি এ(অনার্স)',
            'বি কম' => 'বি কম',
            'বি কম(অনার্স)' => 'বি কম(অনার্স)',
            'এম এ' => 'এম এ',
            'এম এস সি' => 'এম এস সি',
            'এম কম' => 'এম কম',
            'পি এইচ ডি' => 'পি এইচ ডি',
            'এম বি বি এস' => 'এম বি বি এস',
        );
    }

    private function disabilityList()
    {
        return array(
            'সমস্যা নেই' => 'সমস্যা নেই',
            'বাক' => 'বাক',
            'দৃষ্টি' => 'দৃষ্টি',
            'শ্রবন' => 'শ্রবন',
            'শারিরিক' => 'শারিরিক',
            'মানসিক' => 'মানসিক',
        );
    }

    private function bloodGroupList()
    {
        return array(
            'O+ (O Positive)' => 'O+ (O Positive)',
            'O- (O Negative)' => 'O-  (O Negative)',
            'A+ (A Positive)' => 'A+ (A Positive)',
            'A-	(A Negative)' => 'A- (A Negative)',
            'B+ (B Positive)' => 'B+ (B Positive)',
            'B- (B Negative)' => 'B- (B Negative)',
            'AB+ (AB Positive)' => 'AB+ (AB Positive)',
            'AB- (AB  Negative)' => 'AB- (AB Negative)',
        );
    }
}
