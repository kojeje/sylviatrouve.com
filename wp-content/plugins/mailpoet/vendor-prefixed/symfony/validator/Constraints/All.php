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
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class All extends \MailPoetVendor\Symfony\Component\Validator\Constraints\Composite
{
    public $constraints = [];
    public function getDefaultOption()
    {
        return 'constraints';
    }
    public function getRequiredOptions()
    {
        return ['constraints'];
    }
    protected function getCompositeOption()
    {
        return 'constraints';
    }
}
