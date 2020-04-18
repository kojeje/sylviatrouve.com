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


use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Callback;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequenceProvider;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
/**
 * Loads validation metadata using a Doctrine annotation {@link Reader}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AnnotationLoader implements \MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderInterface
{
    protected $reader;
    public function __construct(\MailPoetVendor\Doctrine\Common\Annotations\Reader $reader)
    {
        $this->reader = $reader;
    }
    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(\MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata $metadata)
    {
        $reflClass = $metadata->getReflectionClass();
        $className = $reflClass->name;
        $success = \false;
        foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
            if ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence) {
                $metadata->setGroupSequence($constraint->groups);
            } elseif ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequenceProvider) {
                $metadata->setGroupSequenceProvider(\true);
            } elseif ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraint) {
                $metadata->addConstraint($constraint);
            }
            $success = \true;
        }
        foreach ($reflClass->getProperties() as $property) {
            if ($property->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                    if ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraint) {
                        $metadata->addPropertyConstraint($property->name, $constraint);
                    }
                    $success = \true;
                }
            }
        }
        foreach ($reflClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getMethodAnnotations($method) as $constraint) {
                    if ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraints\Callback) {
                        $constraint->callback = $method->getName();
                        $metadata->addConstraint($constraint);
                    } elseif ($constraint instanceof \MailPoetVendor\Symfony\Component\Validator\Constraint) {
                        if (\preg_match('/^(get|is|has)(.+)$/i', $method->name, $matches)) {
                            $metadata->addGetterMethodConstraint(\lcfirst($matches[2]), $matches[0], $constraint);
                        } else {
                            throw new \MailPoetVendor\Symfony\Component\Validator\Exception\MappingException(\sprintf('The constraint on "%s::%s" cannot be added. Constraints can only be added on methods beginning with "get", "is" or "has".', $className, $method->name));
                        }
                    }
                    $success = \true;
                }
            }
        }
        return $success;
    }
}
