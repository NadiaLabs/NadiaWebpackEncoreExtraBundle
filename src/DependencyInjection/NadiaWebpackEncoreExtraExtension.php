<?php

/**
 * This file is part of the NadiaWebpackEncoreExtraBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaWebpackEncoreExtraBundle\DependencyInjection;

use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Asset\EncoreTagRenderer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class NadiaWebpackEncoreExtraExtension
 */
class NadiaWebpackEncoreExtraExtension extends Extension
{
    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $defEncoreTagRenderer = $container->getDefinition(EncoreTagRenderer::class);
        $defEncoreTagRenderer->setArgument(2, $this->getEncoreTagRendererBuilds($config));
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return 'http://nadialabs.com.tw/schema/dic/webpack-encore-extra';
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function getEncoreTagRendererBuilds(array $config)
    {
        $builds = [];

        if (!empty($config['default_build']) && !empty($config['default_build']['controller_class_name_prefixes'])) {
            $defaultBuild = $config['default_build'];
            $defaultBuild['encore_build_name'] = '_default';

            $builds['_default'] = $defaultBuild;
        }

        foreach ($config['builds'] as $buildName => $build) {
            if (empty($build['controller_class_name_prefixes'])) {
                continue;
            }

            if (empty($build['encore_build_name'])) {
                $build['encore_build_name'] = $buildName;
            }

            $builds[$buildName] = $build;
        }

        return $builds;
    }
}
