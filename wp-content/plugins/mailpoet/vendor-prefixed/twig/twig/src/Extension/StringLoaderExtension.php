<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Twig\Extension;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\TwigFunction;
/**
 * @final
 */
class StringLoaderExtension extends \MailPoetVendor\Twig\Extension\AbstractExtension
{
    public function getFunctions()
    {
        return [new \MailPoetVendor\Twig\TwigFunction('template_from_string', 'twig_template_from_string', ['needs_environment' => \true])];
    }
    public function getName()
    {
        return 'string_loader';
    }
}
\class_alias('MailPoetVendor\\Twig\\Extension\\StringLoaderExtension', 'MailPoetVendor\\Twig_Extension_StringLoader');
namespace MailPoetVendor;

use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\TemplateWrapper;
/**
 * Loads a template from a string.
 *
 *     {{ include(template_from_string("Hello {{ name }}")) }}
 *
 * @param string $template A template as a string or object implementing __toString()
 * @param string $name     An optional name of the template to be used in error messages
 *
 * @return TemplateWrapper
 */
function twig_template_from_string(\MailPoetVendor\Twig\Environment $env, $template, $name = null)
{
    return $env->createTemplate((string) $template, $name);
}
