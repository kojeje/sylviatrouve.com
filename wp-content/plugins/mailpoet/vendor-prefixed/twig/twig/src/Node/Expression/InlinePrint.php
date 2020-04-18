<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Twig\Node\Expression;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Node;
/**
 * @internal
 */
final class InlinePrint extends \MailPoetVendor\Twig\Node\Expression\AbstractExpression
{
    public function __construct(\MailPoetVendor\Twig\Node\Node $node, $lineno)
    {
        parent::__construct(['node' => $node], [], $lineno);
    }
    public function compile(\MailPoetVendor\Twig\Compiler $compiler)
    {
        $compiler->raw('print (')->subcompile($this->getNode('node'))->raw(')');
    }
}
