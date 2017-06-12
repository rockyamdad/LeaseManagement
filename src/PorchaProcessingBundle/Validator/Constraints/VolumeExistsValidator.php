<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use AppBundle\Traits\EntityAssistant;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VolumeExistsValidator extends ConstraintValidator
{
    protected $container;

    public function __construct(Container $container) {

        $this->container = $container;
    }

    public function validate($protocol, Constraint $constraint)
    {
        $ret = $this->container->get('porcha_processing.service.volume_manager')->checkVolumeNoExists($this->container->get('request')->request->all());

        if ($ret) {
            $this->context->buildViolation($this->container->get('translator')->trans('This volume No already exists'))
                ->atPath('property')
                ->setParameter('{{ value }}', '')
                ->addViolation();
        }
    }
}