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


/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Daniel Holmes <daniel@danielholmes.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LessThan extends \MailPoetVendor\Symfony\Component\Validator\Constraints\AbstractComparison
{
    const TOO_HIGH_ERROR = '079d7420-2d13-460c-8756-de810eeb37d2';
    protected static $errorNames = [self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR'];
    public $message = 'This value should be less than {{ compared_value }}.';
}
