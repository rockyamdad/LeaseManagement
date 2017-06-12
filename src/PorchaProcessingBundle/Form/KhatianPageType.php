<?php

namespace PorchaProcessingBundle\Form;

use PorchaProcessingBundle\Entity\Khatian;
use PorchaProcessingBundle\Entity\KhatianLog;
use PorchaProcessingBundle\Entity\Volume;
use PorchaProcessingBundle\Validator\Constraints\KhatianExist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use PorchaProcessingBundle\Util\PlaceHolders;

class KhatianPageType extends AbstractType
{
    /**
     * @var Khatian
     */
    private $khatian;
    private $pageType;

    public function __construct(Khatian $khatian = null, $pageType = '')
    {
        $this->khatian = $khatian;
        $this->pageType = $pageType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('volumeId', 'hidden', array(
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
                'data' => ($this->khatian) ? $this->khatian->getVolume()->getId() : '',
                )
            )
            ->add('rs_no', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getRsNo() : '',
                    'required' => true,
                    'read_only' => (strtoupper($this->pageType) == 'PAGE1') ? false : true,
                )
            )
            ->add('pargana', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getPargana() : '',
                    'required' => true,
                    'read_only' => (strtoupper($this->pageType) == 'PAGE1') ? false : true,
                )
            )
            ->add('taugi_no', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getTaugiNo() : '',
                    'required' => true,
                    'read_only' => (strtoupper($this->pageType) == 'PAGE1') ? false : true,
                )
            )
            ->add('district', 'text', array(
                'attr' => array('class' => 'form-control'),
                'read_only' => true,
                'mapped' => false,
                    'data' => ($this->khatian && $this->khatian->getJlnumber()) ? $this->khatian->getJlnumber()->getDistrict() : ''
                )
            )
            ->add('upozila', 'text', array(
                'attr' => array('class' => 'form-control'),
                'read_only' => true,
                'mapped' => false,
                'data' => ($this->khatian && $this->khatian->getVolume()->getUpozila()) ? $this->khatian->getVolume()->getUpozila()->getName() : ''
                )
            )
            ->add('thana', 'text', array(
                'attr' => array('class' => 'form-control'),
                'read_only' => true,
                'mapped' => false,
                'data' => ($this->khatian && $this->khatian->getJlnumber()) ? $this->khatian->getJlnumber()->getThana() : ''
                )
            )
            ->add('mouzaMapReference', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getMouzaMapReference() : '',
                    'required' => false,
                )
            )
            ->add('mouza', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'read_only' => true,
                    'mapped' => false,
                    'data' => ($this->khatian && $this->khatian->getMouza()) ? $this->khatian->getMouza()->getName() : ''
                )
            )
            ->add('jl_no', 'text', array(
                    'attr' => array('class' => 'form-control'),
                    'read_only' => true,
                    'mapped' => false,
                    'data' => ($this->khatian && $this->khatian->getJlnumber()) ? $this->khatian->getJlnumber()->getName() : ''
                )
            );

        if (strtoupper($this->pageType) == 'PAGE1') {

            $builder->add('khatian_no', 'text', array(
                    'attr' => array('class' => 'form-control bn-digit', 'maxlength' => '5'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getKhatianNo() : '',
                    'required' => true,
                    'read_only' => false,
                    'constraints' => new KhatianExist(),
                )
            )
            ;

        } else {

            $builder->add('khatian_no', 'text', array(
                    'attr' => array('class' => 'form-control', 'maxlength' => '5'),
                    'mapped' => false,
                    'data' => ($this->khatian) ? $this->khatian->getKhatianNo() : '',
                    'read_only' => true,
                )
            )
            ;
        }

        $columnPropertyMap = PlaceHolders::getKhatianPageColumnPropertyMapping();
        foreach (PlaceHolders::getFields() as $field => $label) {

            if (!in_array($field, array('district', 'upozila', 'thana', 'pargana', 'mouza', 'jl_no', 'khatian_no', 'rs_no', 'taugi_no'))) {

                $fieldAttr = PlaceHolders::getFieldAttr($field);

                $fieldProp = array();
                $attr = array();

                $attr['placeholder'] = (!empty($fieldAttr['attr_placeholder'])) ? $fieldAttr['attr_placeholder'] : '';
                $attr['class'] = (!empty($fieldAttr['attr_class'])) ? $fieldAttr['attr_class'] : 'form-control';
                if (!empty($fieldAttr['attr_cols'])) {
                    $attr['cols'] = $fieldAttr['attr_cols'];
                }
                if (!empty($fieldAttr['attr_rows'])) {
                    $attr['rows'] = $fieldAttr['attr_rows'];
                }
                $fieldProp['attr'] = $attr;
                $fieldProp['required'] = (!empty($fieldAttr['required'])) ? $fieldAttr['required'] : false;
                $fieldProp['read_only'] = (!empty($fieldAttr['read_only'])) ? $fieldAttr['read_only'] : false;
                $fieldProp['mapped'] = (isset($fieldAttr['mapped'])) ? $fieldAttr['mapped'] : true;
                if (isset($fieldAttr['trim'])) {
                    $fieldProp['trim'] = $fieldAttr['trim'];
                }

                $builder->add($columnPropertyMap[$field], $fieldAttr['type'], $fieldProp);
            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PorchaProcessingBundle\Entity\KhatianPage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'khatian_page';
    }
}