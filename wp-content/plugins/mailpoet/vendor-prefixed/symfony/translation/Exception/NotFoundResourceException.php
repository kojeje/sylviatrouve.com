<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Translation\Exception;

if (!defined('ABSPATH')) exit;


/**
 * Thrown when a resource does not exist.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class NotFoundResourceException extends \InvalidArgumentException implements \MailPoetVendor\Symfony\Component\Translation\Exception\ExceptionInterface
{
}
