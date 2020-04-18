<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
/**
 * Validator for Callback constraint.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CallbackValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($object, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Callback) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\Callback::class);
        }
        $method = $constraint->callback;
        if ($method instanceof \Closure) {
            $method($object, $this->context, $constraint->payload);
        } elseif (\is_array($method)) {
            if (!\is_callable($method)) {
                if (isset($method[0]) && \is_object($method[0])) {
                    $method[0] = \get_class($method[0]);
                }
                throw new \MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException(\sprintf('%s targeted by Callback constraint is not a valid callable', \json_encode($method)));
            }
            \call_user_func($method, $object, $this->context, $constraint->payload);
        } elseif (null !== $object) {
            if (!\method_exists($object, $method)) {
                throw new \MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException(\sprintf('Method "%s" targeted by Callback constraint does not exist in class %s', $method, \get_class($object)));
            }
            $reflMethod = new \ReflectionMethod($object, $method);
            if ($reflMethod->isStatic()) {
                $reflMethod->invoke(null, $object, $this->context, $constraint->payload);
            } else {
                $reflMethod->invoke($object, $this->context, $constraint->payload);
            }
        }
    }
}
