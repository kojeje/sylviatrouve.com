<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Validator;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface;
/**
 * Collects some data about validator calls.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class TraceableValidator implements \MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface
{
    private $validator;
    private $collectedData = [];
    public function __construct(\MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    /**
     * @return array
     */
    public function getCollectedData()
    {
        return $this->collectedData;
    }
    public function reset()
    {
        $this->collectedData = [];
    }
    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
        return $this->validator->getMetadataFor($value);
    }
    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        return $this->validator->hasMetadataFor($value);
    }
    /**
     * {@inheritdoc}
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        $violations = $this->validator->validate($value, $constraints, $groups);
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 7);
        $file = $trace[0]['file'];
        $line = $trace[0]['line'];
        for ($i = 1; $i < 7; ++$i) {
            if (isset($trace[$i]['class'], $trace[$i]['function']) && 'validate' === $trace[$i]['function'] && \is_a($trace[$i]['class'], \MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface::class, \true)) {
                $file = $trace[$i]['file'];
                $line = $trace[$i]['line'];
                while (++$i < 7) {
                    if (isset($trace[$i]['function'], $trace[$i]['file']) && empty($trace[$i]['class']) && 0 !== \strpos($trace[$i]['function'], 'call_user_func')) {
                        $file = $trace[$i]['file'];
                        $line = $trace[$i]['line'];
                        break;
                    }
                }
                break;
            }
        }
        $name = \str_replace('\\', '/', $file);
        $name = \substr($name, \strrpos($name, '/') + 1);
        $this->collectedData[] = ['caller' => \compact('name', 'file', 'line'), 'context' => \compact('value', 'constraints', 'groups'), 'violations' => \iterator_to_array($violations)];
        return $violations;
    }
    /**
     * {@inheritdoc}
     */
    public function validateProperty($object, $propertyName, $groups = null)
    {
        return $this->validator->validateProperty($object, $propertyName, $groups);
    }
    /**
     * {@inheritdoc}
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
        return $this->validator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
    }
    /**
     * {@inheritdoc}
     */
    public function startContext()
    {
        return $this->validator->startContext();
    }
    /**
     * {@inheritdoc}
     */
    public function inContext(\MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextInterface $context)
    {
        return $this->validator->inContext($context);
    }
}
