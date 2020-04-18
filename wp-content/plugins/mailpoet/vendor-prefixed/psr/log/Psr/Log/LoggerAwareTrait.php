<?php

namespace MailPoetVendor\Psr\Log;

if (!defined('ABSPATH')) exit;


/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\MailPoetVendor\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
