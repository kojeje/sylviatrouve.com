<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Exception;

if (!defined('ABSPATH')) exit;


class UnexpectedTypeException extends \MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException
{
    public function __construct($value, $expectedType)
    {
        parent::__construct(\sprintf('Expected argument of type "%s", "%s" given', $expectedType, \is_object($value) ? \get_class($value) : \gettype($value)));
    }
}
