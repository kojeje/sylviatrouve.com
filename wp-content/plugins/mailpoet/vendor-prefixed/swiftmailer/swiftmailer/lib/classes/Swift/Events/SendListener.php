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
 * Listens for Messages being sent from within the Transport system.
 *
 * @author Chris Corbyn
 */
interface Swift_Events_SendListener extends \MailPoetVendor\Swift_Events_EventListener
{
    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt);
    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\MailPoetVendor\Swift_Events_SendEvent $evt);
}
