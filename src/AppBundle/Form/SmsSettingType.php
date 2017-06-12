<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SmsSettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('applicationType', 'choice', array(
                'required'    => true,
                'attr' => array('class' => 'form-control select2 input-large application_type'),
                'choices'  => array('PORCHA' => 'PORCHA', 'INFORMATION_SLIP' => 'INFORMATION_SLIP', 'CASE_COPY' => 'Case copy', 'MOUZA_MAP' => 'Mouza Map'),
            ))
            ->add('newApplicationMessage', 'textarea', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'আপনার আবেদন আইডিঃ %id%। বিতরণের তারিখঃ  %date%'
                    ),
                )
            )
            ->add('deliveryChangeMessage', 'textarea', array(
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'আপনার আবেদন আইডিঃ %id%। পরিবর্তিত বিতরণের তারিখঃ  %date%'
                    )
                )
            )
            ->add('active', 'choice', array(
                'attr' => array('class' => 'form-control select2 input-small'),
                'choices'  => array('0' => 'Disable', '1' => 'Enable'),
            ))
            ->add('locked', 'checkbox', array(
                    'label'    => 'Locked',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SmsSetting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sms_setting';
    }
}