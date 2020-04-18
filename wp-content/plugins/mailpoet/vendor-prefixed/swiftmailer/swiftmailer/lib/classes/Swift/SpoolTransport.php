<?php

namespace MailPoetVendor;

if (!defined('ABSPATH')) exit;


/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Stores Messages in a queue.
 *
 * @author Fabien Potencier
 */
class Swift_SpoolTransport extends \MailPoetVendor\Swift_Transport_SpoolTransport
{
    /**
     * Create a new SpoolTransport.
     */
    public function __construct(\MailPoetVendor\Swift_Spool $spool)
    {
        $arguments = \MailPoetVendor\Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.spool');
        $arguments[] = $spool;
        \call_user_func_array([$this, 'MailPoetVendor\\Swift_Transport_SpoolTransport::__construct'], $arguments);
    }
}
