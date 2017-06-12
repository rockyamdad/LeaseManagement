<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class MouzaMapRequestType extends ServiceRequestType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('detail_entities');
        $builder->add('detail_entities', 'collection', array(
            'type' => new MouzaMapRequestPorchaType($this->districtIds),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'disabled' => $this->disableFields['applicationInfo']
        ))
        ;
    }
}
