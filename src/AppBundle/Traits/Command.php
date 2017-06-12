<?php
/*
 * This file is part of the IBBL project.
 *
 * (c) Anis Uddin Ahmad <anisniit@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AppBundle\Traits;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;

trait Command
{
    /**
     * @var OutputInterface
     */
    protected $_out;

    /**
     * @var EntityManager
     */
    protected $_em;

    protected function say($content, $level = OutputInterface::VERBOSITY_NORMAL)
    {
        if(is_null($this->_out)) {
            throw new \RuntimeException('No OutputInterface was set to AppBundle\Traits\Command::$_out');
        }

        if($this->_out->getVerbosity() >= $level) {
            $this->_out->writeln(date('[Y-m-d H:i:s] ') .$content);
        }
    }

} 