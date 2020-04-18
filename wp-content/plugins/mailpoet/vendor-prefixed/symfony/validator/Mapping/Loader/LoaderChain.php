<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
/**
 * Loads validation metadata from multiple {@link LoaderInterface} instances.
 *
 * Pass the loaders when constructing the chain. Once
 * {@link loadClassMetadata()} is called, that method will be called on all
 * loaders in the chain.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LoaderChain implements \MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderInterface
{
    protected $loaders;
    /**
     * @param LoaderInterface[] $loaders The metadata loaders to use
     *
     * @throws MappingException If any of the loaders has an invalid type
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof \MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderInterface) {
                throw new \MailPoetVendor\Symfony\Component\Validator\Exception\MappingException(\sprintf('Class %s is expected to implement LoaderInterface', \get_class($loader)));
            }
        }
        $this->loaders = $loaders;
    }
    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(\MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata $metadata)
    {
        $success = \false;
        foreach ($this->loaders as $loader) {
            $success = $loader->loadClassMetadata($metadata) || $success;
        }
        return $success;
    }
    /**
     * @return LoaderInterface[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }
}
