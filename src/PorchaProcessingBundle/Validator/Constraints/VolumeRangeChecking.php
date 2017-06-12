<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VolumeRangeChecking extends Constraint
{
    public $message = "check the khatian range";

    public function validatedBy()
    {
        return 'volume_range_checking';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}