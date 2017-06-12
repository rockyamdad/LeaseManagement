<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class CorrectionRequestType extends ServiceRequestType
{
    protected $districtIds;
    protected $officeType;
    /**
     * CorrectionRequestType constructor.
     */
    public function __construct($districtIds,$officeType)
    {
        $this->officeType = $officeType;
        $this->districtIds = $districtIds;


    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ))
            ->add('email', null, array(
                'required' => false,
            ))

            ->add('detail_entities', 'collection', array(
            'type' => new CorrectionRequestPorchaType($this->districtIds),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'disabled' => $this->disableFields['applicationInfo']
        ));
    }

}
