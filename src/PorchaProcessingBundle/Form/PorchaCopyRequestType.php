<?php

namespace PorchaProcessingBundle\Form;

use AppBundle\Form\AcLandDocumentType;
use Doctrine\ORM\EntityRepository;
use Proxies\__CG__\AppBundle\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PorchaCopyRequestType extends AbstractType
{

    protected $office;
    protected $porchaCopyId;

    public function __construct(Office $office = null,$porchaCopyId = null) {

        $this->office = $office;
        $this->porchaCopyId = $porchaCopyId;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('requestedVolumeKhatian', null, array(
                'required' => true,
                'disabled' =>$this->porchaCopyId ? true:false,
                'attr' => array(
                    'rows' => 6
                )

            ))
            ->add('subject', null, array(
                'required' => true,
                'disabled' =>$this->porchaCopyId ? true:false
            ))
            ->add('requestReason', null, array(
                'required' => true,
                'disabled' =>$this->porchaCopyId ? true:false,
                'attr' => array(
                    'rows' => 6
                )
            ));
            if($this->office) {

                $builder->add('toOffice', 'entity', array(
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'property' => 'name',
                    'class' => 'AppBundle:Office',
                    'disabled' =>$this->porchaCopyId ? true:false,
                    'empty_value'=>'Select',
                    'query_builder' => function (EntityRepository $er) {
                        $qb = $er->createQueryBuilder('o');
                        $qb->where("o.type = 'AC_LAND' ");
                        $qb->andWhere("o.parent = :office ")->setParameter('office', $this->office);
                        return $qb->andWhere("o.active = 1 ");
                    },
                ));
                if($this->porchaCopyId) {
                    $builder->add(
                        'requestResponse',
                        null,
                        array(
                            'required' => true,
                            'attr'     => array(
                                'rows' => 6
                            )
                        ));
                    $builder->add('documents', 'file',array(
                        'multiple'=>true,
                        'mapped'=>false
                    ));

                }
            
            }
        $builder->add('documents', 'file',array(
            'multiple'=>true,
            'mapped'=>false
        ));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\PorchaCopyRequest',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porchaprocessingbundle_porchacopyrequest';
    }
}
