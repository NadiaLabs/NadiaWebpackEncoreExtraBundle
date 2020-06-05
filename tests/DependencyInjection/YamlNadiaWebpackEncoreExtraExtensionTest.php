<?php

/*
 * This file is part of the NadiaWebpackEncoreExtraBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class YamlNadiaWebpackEncoreExtraExtensionTest
 */
class YamlNadiaWebpackEncoreExtraExtensionTest extends NadiaWebpackEncoreExtraExtensionTest
{
    protected function loadConfigFile(ContainerBuilder $container, $filename)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures/config'));
        $loader->load($filename . '.yaml');
    }
}
