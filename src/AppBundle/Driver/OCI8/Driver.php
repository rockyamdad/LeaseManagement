<?php

namespace AppBundle\Driver\OCI8;

use Doctrine\DBAL\Driver\OCI8\Driver as BaseDriver;
use Doctrine\DBAL\Platforms;

/**
 * A Doctrine DBAL driver for the Oracle OCI8 PHP extensions.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
class Driver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        return new OCI8Connection(
            $username,
            $password,
            $this->_constructDsn($params),
            isset($params['charset']) ? $params['charset'] : null,
            isset($params['sessionMode']) ? $params['sessionMode'] : OCI_DEFAULT,
            isset($params['persistent']) ? $params['persistent'] : false
        );
    }

}
