<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Proxies\__CG__\PorchaProcessingBundle\Entity\KhatianLog;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class KhatianExistValidator extends ConstraintValidator
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $entityManager, Container $container) {

        $this->em = $entityManager;
        $this->container = $container;
    }

    public function validate($protocol, Constraint $constraint)
    {
        if (false) {

            $this->context->buildViolation('Khatian No exists')
                ->atPath('property')
                ->setParameter('{{ value }}', '')
                ->addViolation();

        }
    }
}