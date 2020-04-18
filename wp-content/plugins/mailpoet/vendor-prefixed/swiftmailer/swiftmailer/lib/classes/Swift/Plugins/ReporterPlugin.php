<?php

namespace MailPoetVendor;

if (!defined('ABSPATH')) exit;


/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Does real time reporting of pass/fail for each recipient.
 *
 * @author Chris Corbyn
 */
class Swift_Plugins_ReporterPlugin implements \MailPoetVendor\Swift_Events_SendListener
{
    /**
     * The reporter backend which takes notifications.
     *
     * @var Swift_Plugins_Reporter
     */
    private $reporter;
    /**
     * Create a new ReporterPlugin using $reporter.
     */
    public function __construct(\MailPoetVendor\Swift_Plugins_Reporter $reporter)
    {
        $this->reporter = $reporter;
    }
    /**
     * Not used.
     */
    public function beforeSendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt)
    {
    }
    /**
     * Invoked immediately after the Message is sent.
     */
    public function sendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $failures = \array_flip($evt->getFailedRecipients());
        foreach ((array) $message->getTo() as $address => $null) {
            $this->reporter->notify($message, $address, \array_key_exists($address, $failures) ? \MailPoetVendor\Swift_Plugins_Reporter::RESULT_FAIL : \MailPoetVendor\Swift_Plugins_Reporter::RESULT_PASS);
        }
        foreach ((array) $message->getCc() as $address => $null) {
            $this->reporter->notify($message, $address, \array_key_exists($address, $failures) ? \MailPoetVendor\Swift_Plugins_Reporter::RESULT_FAIL : \MailPoetVendor\Swift_Plugins_Reporter::RESULT_PASS);
        }
        foreach ((array) $message->getBcc() as $address => $null) {
            $this->reporter->notify($message, $address, \array_key_exists($address, $failures) ? \MailPoetVendor\Swift_Plugins_Reporter::RESULT_FAIL : \MailPoetVendor\Swift_Plugins_Reporter::RESULT_PASS);
        }
    }
}
