<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VolumeRangeCheckingValidator extends ConstraintValidator
{
    protected $container;

    public function __construct(Container $container) {

        $this->container = $container;
    }

    public function validate($protocol, Constraint $constraint)
    {
        $ret = $this->container->get('porcha_processing.service.volume_manager')->checkVolumeNoExists($this->container->get('request')->request->all(), true);

        if ($ret) {
            $this->context->buildViolation($this->container->get('translator')->trans('check the khatian range'))
                ->atPath('property')
                ->setParameter('{{ value }}', '')
                ->addViolation();
        }
    }
}