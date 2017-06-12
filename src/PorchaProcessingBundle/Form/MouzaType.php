<?php

namespace PorchaProcessingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MouzaType extends AbstractType
{
    private $jlnumbers;
    private $relatedDistrictIds;
    protected $includeApproved;
    protected $disable;

    public function __construct($jlnumbers = null, $relatedDistrictIds = null, $includeApproved = null, $disable = false)
    {
        $this->jlnumbers = $jlnumbers;
        $this->relatedDistrictIds = $relatedDistrictIds;
        $this->includeApproved = $includeApproved;
        $this->disable = $disable;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('upozila', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium mo-district'),
                    'class' => 'PorchaProcessingBundle\Entity\Upozila',
                    'placeholder' => 'Select',
                    'disabled' => ($this->disable) ? $this->disable : false,
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                        $qb = $er->createQueryBuilder('u');
                        $qb->where('u.deleted = 0');
                        $qb->andWhere('u.approved = 1');

                        if (!empty($this->relatedDistrictIds)) {
                            $qb->andwhere('u.district IN (:id)');
                            $qb->setParameter('id', array_values($this->relatedDistrictIds));
                        }

                        return $qb;
                    },
                )
            );

        $builder->add('csJLNumberId', 'hidden', array(
                'mapped' => false,
                'data' => (isset($this->jlnumbers['CS']['id'])) ? $this->jlnumbers['CS']['id'] : 0,
                )
            )
            ->add('csJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['CS']['name'])) ? $this->jlnumbers['CS']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('csDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'constraints' => new NotBlank(),
                    'data' => (isset($this->jlnumbers['CS']['district'])) ? $this->jlnumbers['CS']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('csThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'constraints' => new NotBlank(),
                    'data' => (isset($this->jlnumbers['CS']['thana'])) ? $this->jlnumbers['CS']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('csMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'constraints' => new NotBlank(),
                    'data' => (isset($this->jlnumbers['CS']['mouzaName'])) ? $this->jlnumbers['CS']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('csDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['CS']['division'])) ? $this->jlnumbers['CS']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                    'required' => false
                )
            );

        $builder->add('saJLNumberId', 'hidden', array(
                'mapped' => false,
                'constraints' => new NotBlank(),
                'data' => (isset($this->jlnumbers['SA']['id'])) ? $this->jlnumbers['SA']['id'] : 0
                )
            )
            ->add('saJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['SA']['name'])) ? $this->jlnumbers['SA']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('saDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['SA']['district'])) ? $this->jlnumbers['SA']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('saThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['SA']['thana'])) ? $this->jlnumbers['SA']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('saMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['SA']['mouzaName'])) ? $this->jlnumbers['SA']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('saDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['SA']['division'])) ? $this->jlnumbers['SA']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                )
            );

        $builder->add('rsJLNumberId', 'hidden', array(
                'mapped' => false,
                'data' => (isset($this->jlnumbers['RS']['id'])) ? $this->jlnumbers['RS']['id'] : 0
                )
            )
            ->add('rsJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['RS']['name'])) ? $this->jlnumbers['RS']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('rsDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['RS']['district'])) ? $this->jlnumbers['RS']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('rsThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['RS']['thana'])) ? $this->jlnumbers['RS']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('rsMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'constraints' => new NotBlank(),
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['RS']['mouzaName'])) ? $this->jlnumbers['RS']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('rsDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['RS']['division'])) ? $this->jlnumbers['RS']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                )
            )
        ;

        $builder->add('bsJLNumberId', 'hidden', array(
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['BS']['id'])) ? $this->jlnumbers['BS']['id'] : 0
                )
            )
            ->add('bsJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['BS']['name'])) ? $this->jlnumbers['BS']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('bsDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['BS']['district'])) ? $this->jlnumbers['BS']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('bsThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['BS']['thana'])) ? $this->jlnumbers['BS']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('bsMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['BS']['mouzaName'])) ? $this->jlnumbers['BS']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('bsDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['BS']['division'])) ? $this->jlnumbers['BS']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                )
            );

        $builder->add('petyJLNumberId', 'hidden', array(
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['PETY']['id'])) ? $this->jlnumbers['PETY']['id'] : 0
                )
            )
            ->add('petyJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['PETY']['name'])) ? $this->jlnumbers['PETY']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('petyDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['PETY']['district'])) ? $this->jlnumbers['PETY']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('petyThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['PETY']['thana'])) ? $this->jlnumbers['PETY']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('petyMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['PETY']['mouzaName'])) ? $this->jlnumbers['PETY']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('petyDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['PETY']['division'])) ? $this->jlnumbers['PETY']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                )
            );

        $builder->add('diaraJLNumberId', 'hidden', array(
                    'mapped' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['id'])) ? $this->jlnumbers['DIARA']['id'] : 0
                )
            )
            ->add('diaraJLNumber', 'text', array(
                    'attr' => array('class' => 'form-control input-medium', 'maxlength' => '5'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['name'])) ? $this->jlnumbers['DIARA']['name'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('diaraDistrict', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['district'])) ? $this->jlnumbers['DIARA']['district'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('diaraThana', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['thana'])) ? $this->jlnumbers['DIARA']['thana'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('diaraMouzaName', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,                     'required' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['mouzaName'])) ? $this->jlnumbers['DIARA']['mouzaName'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false
                )
            )
            ->add('diaraDivision', 'text', array(
                    'attr' => array('class' => 'form-control input-medium'),
                    'mapped' => false,
                    'required' => false,
                    'data' => (isset($this->jlnumbers['DIARA']['division'])) ? $this->jlnumbers['DIARA']['division'] : '',
                    'disabled' => ($this->disable) ? $this->disable : false,
                )
            );

        $builder->add('save', 'submit', array(
                'attr' => array('class' => 'btn green'),
                'disabled' => ($this->disable) ? $this->disable : false
            )
        );

        if ($this->includeApproved) {
            $builder->add(
                'approved', 'checkbox', array(
                    'label'    => 'Approved',
                    'attr'     => array('class' => 'icheck'),
                    'required' => false,
                )
            );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\Mouza'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'porcha_processing_mouza';
    }
}