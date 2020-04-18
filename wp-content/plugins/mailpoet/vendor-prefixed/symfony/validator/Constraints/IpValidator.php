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
 * Validates whether a value is a valid IP address.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class IpValidator extends \MailPoetVendor\Symfony\Component\Validator\ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, \MailPoetVendor\Symfony\Component\Validator\Constraint $constraint)
    {
        if (!$constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException($value, 'string');
        }
        $value = (string) $value;
        switch ($constraint->version) {
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V4:
                $flag = \FILTER_FLAG_IPV4;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V6:
                $flag = \FILTER_FLAG_IPV6;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V4_NO_PRIV:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V6_NO_PRIV:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::ALL_NO_PRIV:
                $flag = \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V4_NO_RES:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V6_NO_RES:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::ALL_NO_RES:
                $flag = \FILTER_FLAG_NO_RES_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V4_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::V6_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case \MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::ALL_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            default:
                $flag = null;
                break;
        }
        if (!\filter_var($value, \FILTER_VALIDATE_IP, $flag)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(\MailPoetVendor\Symfony\Component\Validator\Constraints\Ip::INVALID_IP_ERROR)->addViolation();
        }
    }
}
