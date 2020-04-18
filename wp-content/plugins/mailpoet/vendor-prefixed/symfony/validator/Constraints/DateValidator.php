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
class DateValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    const PATTERN = '/^(\\d{4})-(\\d{2})-(\\d{2})$/';
    /**
     * Checks whether a date is valid.
     *
     * @param int $year  The year
     * @param int $month The month
     * @param int $day   The day
     *
     * @return bool Whether the date is valid
     *
     * @internal
     */
    public static function checkDate($year, $month, $day)
    {
        return \checkdate($month, $day, $year);
    }
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Date) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\Date::class);
        }
        if (null === $value || '' === $value || $value instanceof \DateTimeInterface) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($value, 'string');
        }
        $value = (string) $value;
        if (!\preg_match(static::PATTERN, $value, $matches)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Date::INVALID_FORMAT_ERROR)->addViolation();
            return;
        }
        if (!self::checkDate($matches[1], $matches[2], $matches[3])) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Date::INVALID_DATE_ERROR)->addViolation();
        }
    }
}
