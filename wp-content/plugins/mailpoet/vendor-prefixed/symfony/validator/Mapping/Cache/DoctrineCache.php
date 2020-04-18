<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Cache;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Common\Cache\Cache;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
/**
 * Adapts a Doctrine cache to a CacheInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
final class DoctrineCache implements \MailPoetVendor\Symfony\Component\Validator\Mapping\Cache\CacheInterface
{
    private $cache;
    public function __construct(\MailPoetVendor\Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }
    public function setCache(\MailPoetVendor\Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }
    /**
     * {@inheritdoc}
     */
    public function has($class)
    {
        return $this->cache->contains($class);
    }
    /**
     * {@inheritdoc}
     */
    public function read($class)
    {
        return $this->cache->fetch($class);
    }
    /**
     * {@inheritdoc}
     */
    public function write(\MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata $metadata)
    {
        $this->cache->save($metadata->getClassName(), $metadata);
    }
}
