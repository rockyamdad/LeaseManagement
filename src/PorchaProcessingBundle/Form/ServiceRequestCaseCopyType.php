<?php

namespace PorchaProcessingBundle\Form;

use Doctrine\ORM\EntityRepository;
use PorchaProcessingBundle\EventListener\AddMouzaFieldSubscriber;
use PorchaProcessingBundle\EventListener\AddUpozilaFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ServiceRequestCaseCopyType extends AbstractType
{
    protected $officeType;
    protected $disableFields;

    public function __construct($officeType)
    {
        $this->officeType = $officeType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $serviceRequest = $options['data']->getServiceRequest();
        $this->disableFields = $options['data']->disableFields;
        $serviceRequest->disableFields = $this->disableFields;

        $builder->add('plaintiffDefendant', null, array(
            'attr' => array(
                'rows' => 4
            ),
            'disabled' => $this->disableFields['applicationInfo']
        ));
        $builder->add('lawyerName', null, array(
            'disabled' => $this->disableFields['applicationInfo']
        ));
        $builder->add('caseNo', null, array(
            'disabled' => $this->disableFields['applicationInfo']
        ));
        $builder->add('courtName', null, array(
            'disabled' => $this->disableFields['applicationInfo']
        ));
        $serviceRequestForm = new ServiceRequestType(null, $this->officeType);

        $builder->add('serviceRequest', $serviceRequestForm, array(
            'data' => $serviceRequest
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\ServiceRequestCaseCopy'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porchaprocessingbundle_servicerequestcasecopy';
    }
}
