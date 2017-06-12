<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceRequestType extends AbstractType
{
    protected $districtIds;
    protected $officeType;
    protected $disableFields;

    public function __construct($districtIds,$officeType)
    {
        $this->districtIds = $districtIds;
        $this->officeType = $officeType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']->getId();
        $applicationType = $options['data']->getType();
        $this->disableFields = $options['data']->disableFields;

        if($this->officeType != 'UDC'){
           $methodType =  array('DIRECT' => 'DC Office','POSTAL' => 'Postal');
        } else {
            $methodType = array('UDC' => 'UDC','POSTAL' => 'Postal','DIRECT' => 'DC Office');
        }

        $builder
            ->add('urgency', 'choice', array(
                'choices' => array(
                    'NORMAL' => 'NORMAL',
                    'URGENT' => 'Urgent'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'disabled' => $this->disableFields['paymentInfo']
            ))
            ->add('deliveryMethod', 'choice', array(

                'choices' => $methodType,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'disabled' => $this->disableFields['paymentInfo']
            ))
            ->add('name', null, array(
                'required' => true,
                'disabled' => $this->disableFields['applicantInfo']
            ))
            ->add('contactNumber', null, array(
                'required' => true,
                'attr' => array('class' => 'mask_mobile', 'maxlength' => 11),
                'disabled' => $this->disableFields['applicantInfo']
            ))
            ->add('nid', null, array(
                'required' => false,
                'attr' => array('class' => 'mask_nid' , 'maxlength' => 17),
                'disabled' => $this->disableFields['applicantInfo']
            ))
            ->add('email', null, array(
                'required' => false,
                'disabled' => $this->disableFields['applicantInfo']
            ))
            ->add('postalCode', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('area', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('roadNo', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('houseNo', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('address', null, array(
                'required' => false,
                'attr' => array(
                    'rows' => 6
                ),
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('ongoingCare', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('district', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ))
            ->add('upozila', null, array(
                'required' => false,
                'disabled' => $this->disableFields['deliveryAddress']
            ));

        if (in_array($applicationType, array('INFORMATION_SLIP'))) {
            $builder->add('description', null, array(
                'required' => true,
                'attr' => array(
                    'rows' => 5
                ),
                'disabled' => $this->disableFields['applicationInfo']
            ));
        } else {
            $builder->add('detail_entities', 'collection', array(
                'type' => new ServiceRequestPorchaType($this->districtIds),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'disabled' => $this->disableFields['applicationInfo']
            ));
        }

        if ($id) {
            $builder->add('estimateDeliveryAt', null, array(
                'widget' => 'single_text',
                'format' => 'd-M-y',
                'attr' => array(
                    'class' => 'date-picker'
                ),
                'disabled' => $this->disableFields['deliveryDate']
            ));
        }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\ServiceRequest',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porchaprocessingbundle_servicerequest';
    }
}
