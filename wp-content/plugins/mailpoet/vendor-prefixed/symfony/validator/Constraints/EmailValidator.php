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


use MailPoetVendor\Egulias\EmailValidator\Validation\EmailValidation;
use MailPoetVendor\Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\RuntimeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class EmailValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    private $isStrict;
    /**
     * @param bool $strict
     */
    public function __construct($strict = \false)
    {
        $this->isStrict = $strict;
    }
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Email) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\Email::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($value, 'string');
        }
        $value = (string) $value;
        if (null === $constraint->strict) {
            $constraint->strict = $this->isStrict;
        }
        if ($constraint->strict) {
            if (!\class_exists('MailPoetVendor\\Egulias\\EmailValidator\\EmailValidator')) {
                throw new \MailPoetVendor\Symfony\Component\Validator\Exception\RuntimeException('Strict email validation requires egulias/email-validator ~1.2|~2.0');
            }
            $strictValidator = new \MailPoetVendor\Egulias\EmailValidator\EmailValidator();
            if (\interface_exists(\MailPoetVendor\Egulias\EmailValidator\Validation\EmailValidation::class) && !$strictValidator->isValid($value, new \MailPoetVendor\Egulias\EmailValidator\Validation\NoRFCWarningsValidation())) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Email::INVALID_FORMAT_ERROR)->addViolation();
                return;
            } elseif (!\interface_exists(\MailPoetVendor\Egulias\EmailValidator\Validation\EmailValidation::class) && !$strictValidator->isValid($value, \false, \true)) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Email::INVALID_FORMAT_ERROR)->addViolation();
                return;
            }
        } elseif (!\preg_match('/^.+\\@\\S+\\.\\S+$/', $value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Email::INVALID_FORMAT_ERROR)->addViolation();
            return;
        }
        $host = (string) \substr($value, \strrpos($value, '@') + 1);
        // Check for host DNS resource records
        if ($constraint->checkMX) {
            if (!$this->checkMX($host)) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Email::MX_CHECK_FAILED_ERROR)->addViolation();
            }
            return;
        }
        if ($constraint->checkHost && !$this->checkHost($host)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Email::HOST_CHECK_FAILED_ERROR)->addViolation();
        }
    }
    /**
     * Check DNS Records for MX type.
     *
     * @param string $host Host
     *
     * @return bool
     */
    private function checkMX($host)
    {
        return '' !== $host && \checkdnsrr($host, 'MX');
    }
    /**
     * Check if one of MX, A or AAAA DNS RR exists.
     *
     * @param string $host Host
     *
     * @return bool
     */
    private function checkHost($host)
    {
        return '' !== $host && ($this->checkMX($host) || (\checkdnsrr($host, 'A') || \checkdnsrr($host, 'AAAA')));
    }
}
