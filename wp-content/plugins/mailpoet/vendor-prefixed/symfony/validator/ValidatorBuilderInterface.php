<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MailPoetVendor\Symfony\Component\Validator;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
/**
 * A configurable builder for ValidatorInterface objects.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface ValidatorBuilderInterface
{
    /**
     * Adds an object initializer to the validator.
     *
     * @return $this
     */
    public function addObjectInitializer(\MailPoetVendor\Symfony\Component\Validator\ObjectInitializerInterface $initializer);
    /**
     * Adds a list of object initializers to the validator.
     *
     * @param ObjectInitializerInterface[] $initializers
     *
     * @return $this
     */
    public function addObjectInitializers(array $initializers);
    /**
     * Adds an XML constraint mapping file to the validator.
     *
     * @param string $path The path to the mapping file
     *
     * @return $this
     */
    public function addXmlMapping($path);
    /**
     * Adds a list of XML constraint mapping files to the validator.
     *
     * @param string[] $paths The paths to the mapping files
     *
     * @return $this
     */
    public function addXmlMappings(array $paths);
    /**
     * Adds a YAML constraint mapping file to the validator.
     *
     * @param string $path The path to the mapping file
     *
     * @return $this
     */
    public function addYamlMapping($path);
    /**
     * Adds a list of YAML constraint mappings file to the validator.
     *
     * @param string[] $paths The paths to the mapping files
     *
     * @return $this
     */
    public function addYamlMappings(array $paths);
    /**
     * Enables constraint mapping using the given static method.
     *
     * @param string $methodName The name of the method
     *
     * @return $this
     */
    public function addMethodMapping($methodName);
    /**
     * Enables constraint mapping using the given static methods.
     *
     * @param string[] $methodNames The names of the methods
     *
     * @return $this
     */
    public function addMethodMappings(array $methodNames);
    /**
     * Enables annotation based constraint mapping.
     *
     * @return $this
     */
    public function enableAnnotationMapping(\MailPoetVendor\Doctrine\Common\Annotations\Reader $annotationReader = null);
    /**
     * Disables annotation based constraint mapping.
     *
     * @return $this
     */
    public function disableAnnotationMapping();
    /**
     * Sets the class metadata factory used by the validator.
     *
     * @return $this
     */
    public function setMetadataFactory(\MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface $metadataFactory);
    /**
     * Sets the cache for caching class metadata.
     *
     * @return $this
     */
    public function setMetadataCache(\MailPoetVendor\Symfony\Component\Validator\Mapping\Cache\CacheInterface $cache);
    /**
     * Sets the constraint validator factory used by the validator.
     *
     * @return $this
     */
    public function setConstraintValidatorFactory(\MailPoetVendor\Symfony\Component\Validator\ConstraintValidatorFactoryInterface $validatorFactory);
    /**
     * Sets the translator used for translating violation messages.
     *
     * @return $this
     */
    public function setTranslator(\MailPoetVendor\Symfony\Component\Translation\TranslatorInterface $translator);
    /**
     * Sets the default translation domain of violation messages.
     *
     * The same message can have different translations in different domains.
     * Pass the domain that is used for violation messages by default to this
     * method.
     *
     * @param string $translationDomain The translation domain of the violation messages
     *
     * @return $this
     */
    public function setTranslationDomain($translationDomain);
    /**
     * Builds and returns a new validator object.
     *
     * @return ValidatorInterface The built validator
     */
    public function getValidator();
}
