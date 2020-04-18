<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Psr\Container\ContainerInterface;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
/**
 * Uses a service container to create constraint validators.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class ContainerConstraintValidatorFactory implements \MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorFactoryInterface
{
    private $container;
    private $validators;
    public function __construct(\MailPoetVendor\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
        $this->validators = [];
    }
    /**
     * {@inheritdoc}
     *
     * @throws ValidatorException      When the validator class does not exist
     * @throws UnexpectedTypeException When the validator is not an instance of ConstraintValidatorInterface
     */
    public function getInstance(\MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        $name = $constraint->validatedBy();
        if (!isset($this->validators[$name])) {
            if ($this->container->has($name)) {
                $this->validators[$name] = $this->container->get($name);
            } else {
                if (!\class_exists($name)) {
                    throw new \MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException(\sprintf('Constraint validator "%s" does not exist or is not enabled. Check the "validatedBy" method in your constraint class "%s".', $name, \get_class($constraint)));
                }
                $this->validators[$name] = new $name();
            }
        }
        if (!$this->validators[$name] instanceof \MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorInterface) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($this->validators[$name], \MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorInterface::class);
        }
        return $this->validators[$name];
    }
}
