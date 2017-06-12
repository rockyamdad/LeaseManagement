<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VolumeExists extends Constraint
{
    public $message = "Volume already exists";

    public function validatedBy()
    {
        return 'volume_exists';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}