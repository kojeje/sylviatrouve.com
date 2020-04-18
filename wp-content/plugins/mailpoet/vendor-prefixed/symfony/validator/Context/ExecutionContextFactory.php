<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Context;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
/**
 * Creates new {@link ExecutionContext} instances.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @internal version 2.5. Code against ExecutionContextFactoryInterface instead.
 */
class ExecutionContextFactory implements \MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextFactoryInterface
{
    private $translator;
    private $translationDomain;
    /**
     * Creates a new context factory.
     *
     * @param TranslatorInterface $translator        The translator
     * @param string|null         $translationDomain The translation domain to
     *                                               use for translating
     *                                               violation messages
     */
    public function __construct(\MailPoetVendor\Symfony\Component\Translation\TranslatorInterface $translator, $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }
    /**
     * {@inheritdoc}
     */
    public function createContext(\MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface $validator, $root)
    {
        return new \MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContext($validator, $root, $this->translator, $this->translationDomain);
    }
}
