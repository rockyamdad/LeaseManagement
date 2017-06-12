<?php

namespace UserBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class BadDesignationException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Invalid NESS designation.';
    }
}
