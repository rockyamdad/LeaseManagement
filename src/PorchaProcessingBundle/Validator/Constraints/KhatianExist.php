<?php
namespace PorchaProcessingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class KhatianExist extends Constraint
{
    public $message = "Khatian already exists";

    public function validatedBy()
    {
        return 'khatian_exist';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}