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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        if (version_compare(Kernel::VERSION, '4.3', '<')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('nadia_webpack_encore_extra');
        } else {
            $treeBuilder = new TreeBuilder('nadia_webpack_encore_extra');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->fixXmlConfig('build')
            ->children()
                ->arrayNode('default_build')
                    ->fixXmlConfig('controller_class_name_prefix')
                    ->fixXmlConfig('file_tree_depth')
                    ->children()
                        ->scalarNode('entry_name_prefix')->defaultValue('')->end()
                        ->scalarNode('package_name')->defaultValue('')->end()
                        ->arrayNode('controller_class_name_prefixes')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('file_tree_depths')
                            ->useAttributeAsKey('controller_class_name_prefix')
                            ->normalizeKeys(false)
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('builds')
                    ->arrayPrototype()
                        ->fixXmlConfig('controller_class_name_prefix')
                        ->fixXmlConfig('file_tree_depth')
                        ->children()
                            ->scalarNode('encore_build_name')->isRequired()->end()
                            ->scalarNode('entry_name_prefix')->defaultValue('')->end()
                            ->scalarNode('package_name')->defaultValue('')->end()
                            ->arrayNode('controller_class_name_prefixes')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('file_tree_depths')
                                ->useAttributeAsKey('controller_class_name_prefix')
                                ->normalizeKeys(false)
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
