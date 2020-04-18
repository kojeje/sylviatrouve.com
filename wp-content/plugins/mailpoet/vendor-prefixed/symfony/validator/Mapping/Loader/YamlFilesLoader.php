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


/**
 * Loads validation metadata from a list of YAML files.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @see FilesLoader
 */
class YamlFilesLoader extends \MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\FilesLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFileLoaderInstance($file)
    {
        return new \MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\YamlFileLoader($file);
    }
}
