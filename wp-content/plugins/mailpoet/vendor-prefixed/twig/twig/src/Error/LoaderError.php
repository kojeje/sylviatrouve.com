<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Twig\Error;

if (!defined('ABSPATH')) exit;


/**
 * Exception thrown when an error occurs during template loading.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LoaderError extends \MailPoetVendor\Twig\Error\Error
{
}
\class_alias('MailPoetVendor\\Twig\\Error\\LoaderError', 'MailPoetVendor\\Twig_Error_Loader');
