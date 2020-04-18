<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Monolog\Handler;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Monolog\Formatter\NormalizerFormatter;
use MailPoetVendor\Monolog\Logger;
/**
 * Handler sending logs to Zend Monitor
 *
 * @author  Christian Bergau <cbergau86@gmail.com>
 * @author  Jason Davis <happydude@jasondavis.net>
 */
class ZendMonitorHandler extends \MailPoetVendor\Monolog\Handler\AbstractProcessingHandler
{
    /**
     * Monolog level / ZendMonitor Custom Event priority map
     *
     * @var array
     */
    protected $levelMap = array();
    /**
     * Construct
     *
     * @param  int                       $level
     * @param  bool                      $bubble
     * @throws MissingExtensionException
     */
    public function __construct($level = \MailPoetVendor\Monolog\Logger::DEBUG, $bubble = \true)
    {
        if (!\function_exists('MailPoetVendor\\zend_monitor_custom_event')) {
            throw new \MailPoetVendor\Monolog\Handler\MissingExtensionException('You must have Zend Server installed with Zend Monitor enabled in order to use this handler');
        }
        //zend monitor constants are not defined if zend monitor is not enabled.
        $this->levelMap = array(\MailPoetVendor\Monolog\Logger::DEBUG => \ZEND_MONITOR_EVENT_SEVERITY_INFO, \MailPoetVendor\Monolog\Logger::INFO => \ZEND_MONITOR_EVENT_SEVERITY_INFO, \MailPoetVendor\Monolog\Logger::NOTICE => \ZEND_MONITOR_EVENT_SEVERITY_INFO, \MailPoetVendor\Monolog\Logger::WARNING => \ZEND_MONITOR_EVENT_SEVERITY_WARNING, \MailPoetVendor\Monolog\Logger::ERROR => \ZEND_MONITOR_EVENT_SEVERITY_ERROR, \MailPoetVendor\Monolog\Logger::CRITICAL => \ZEND_MONITOR_EVENT_SEVERITY_ERROR, \MailPoetVendor\Monolog\Logger::ALERT => \ZEND_MONITOR_EVENT_SEVERITY_ERROR, \MailPoetVendor\Monolog\Logger::EMERGENCY => \ZEND_MONITOR_EVENT_SEVERITY_ERROR);
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->writeZendMonitorCustomEvent(\MailPoetVendor\Monolog\Logger::getLevelName($record['level']), $record['message'], $record['formatted'], $this->levelMap[$record['level']]);
    }
    /**
     * Write to Zend Monitor Events
     * @param string $type Text displayed in "Class Name (custom)" field
     * @param string $message Text displayed in "Error String"
     * @param mixed $formatted Displayed in Custom Variables tab
     * @param int $severity Set the event severity level (-1,0,1)
     */
    protected function writeZendMonitorCustomEvent($type, $message, $formatted, $severity)
    {
        zend_monitor_custom_event($type, $message, $formatted, $severity);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultFormatter()
    {
        return new \MailPoetVendor\Monolog\Formatter\NormalizerFormatter();
    }
    /**
     * Get the level map
     *
     * @return array
     */
    public function getLevelMap()
    {
        return $this->levelMap;
    }
}
