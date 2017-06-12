<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use AppBundle\Traits\EntityAssistant;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BanglaDigitsOnlyValidator extends ConstraintValidator
{
    use EntityAssistant;

    public function validate($protocol, Constraint $constraint)
    {
        if (!empty($protocol) && !is_numeric($this->convertNumber('bn2en', $protocol))) {
            $this->context->buildViolation('Bangla digits only')
                ->atPath('property')
                ->setParameter('{{ value }}', '')
                ->addViolation();
        }
    }
}