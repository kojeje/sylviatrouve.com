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
class IsFalseValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\IsFalse) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\IsFalse::class);
        }
        if (null === $value || \false === $value || 0 === $value || '0' === $value) {
            return;
        }
        $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\IsFalse::NOT_FALSE_ERROR)->addViolation();
    }
}
