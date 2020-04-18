<?php

namespace MailPoetVendor\Psr\Log;

if (!defined('ABSPATH')) exit;


/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\MailPoetVendor\Psr\Log\LoggerInterface $logger);
}
