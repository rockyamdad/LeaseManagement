<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BanglaDigitsOnly extends Constraint
{
    public $message = "Bangla Digits Only";

    public function validatedBy()
    {
        return 'bangla_digits_only';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}