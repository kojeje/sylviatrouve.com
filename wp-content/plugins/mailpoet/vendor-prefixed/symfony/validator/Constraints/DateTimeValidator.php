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
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Diego Saint Esteben <diego@saintesteben.me>
 */
class DateTimeValidator extends \MailPoetVendor\Symfony\Component\Validator\Constraints\DateValidator
{
    /**
     * @deprecated since version 3.1, to be removed in 4.0.
     */
    const PATTERN = '/^(\\d{4})-(\\d{2})-(\\d{2}) (\\d{2}):(\\d{2}):(\\d{2})$/';
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime::class);
        }
        if (null === $value || '' === $value || $value instanceof \DateTimeInterface) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($value, 'string');
        }
        $value = (string) $value;
        \DateTime::createFromFormat($constraint->format, $value);
        $errors = \DateTime::getLastErrors();
        if (0 < $errors['error_count']) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime::INVALID_FORMAT_ERROR)->addViolation();
            return;
        }
        foreach ($errors['warnings'] as $warning) {
            if ('The parsed date was invalid' === $warning) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime::INVALID_DATE_ERROR)->addViolation();
            } elseif ('The parsed time was invalid' === $warning) {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime::INVALID_TIME_ERROR)->addViolation();
            } else {
                $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\DateTime::INVALID_FORMAT_ERROR)->addViolation();
            }
        }
    }
}
