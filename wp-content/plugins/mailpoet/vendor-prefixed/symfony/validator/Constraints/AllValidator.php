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
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AllValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\All) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\All::class);
        }
        if (null === $value) {
            return;
        }
        if (!\is_array($value) && !$value instanceof \Traversable) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($value, 'array or Traversable');
        }
        $context = $this->context;
        $validator = $context->getValidator()->inContext($context);
        foreach ($value as $key => $element) {
            $validator->atPath('[' . $key . ']')->validate($element, $constraint->constraints);
        }
    }
}
