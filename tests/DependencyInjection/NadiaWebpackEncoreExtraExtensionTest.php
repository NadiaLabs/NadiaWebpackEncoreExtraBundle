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

use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Asset\EncoreTagRenderer;
use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\DependencyInjection\NadiaWebpackEncoreExtraExtension;
use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Twig\Extension\AssetExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class AbstractNadiaWebpackEncoreExtraExtensionTest
 */
abstract class NadiaWebpackEncoreExtraExtensionTest extends TestCase
{
    /**
     * @param ContainerBuilder $container
     * @param string           $filename  Filename without extension part (e.g. "test" for test.php/test.xml/test.yml)
     */
    abstract protected function loadConfigFile(ContainerBuilder $container, $filename);

    /**
     * @param $filename
     * @param array $expectedBuilds
     *
     * @dataProvider allDataProvider
     */
    public function testAll($filename, array $expectedBuilds)
    {
        $container = $this->createContainerByConfigFile($filename);

        $this->assertInstanceOf(ContainerBuilder::class, $container);

        $this->assertTrue($container->has(EncoreTagRenderer::class));
        $this->assertTrue($container->has(AssetExtension::class));

        $defEncoreTagRenderer = $container->getDefinition(EncoreTagRenderer::class);

        $this->assertEquals($expectedBuilds, $defEncoreTagRenderer->getArgument(2));
    }

    /**
     * @return array[]
     */
    public function allDataProvider()
    {
        return [
            // Case 1
            ['test-case-1/test', []],
            // Case 2
            [
                'test-case-2/test',
                [
                    '_default' => [
                        'encore_build_name' => '_default',
                        'entry_name_prefix' => 'foo',
                        'package_name' => 'bar',
                        'controller_class_name_prefixes' => [
                            'App\Controller\ClassNamePrefix1',
                            'App\Admin\Controller\ClassNamePrefix2',
                        ],
                        'file_tree_depths' => [
                            'App\Controller\ClassNamePrefix1\SubFolder1' => 2,
                            'App\Controller\ClassNamePrefix1\SubFolder2\SubFolder3' => 3,
                        ],
                    ],
                ]
            ],
            // Case 3
            [
                'test-case-3/test',
                [
                    'foo' => [
                        'encore_build_name' => 'foo',
                        'entry_name_prefix' => 'foo',
                        'package_name' => 'bar',
                        'controller_class_name_prefixes' => [
                            'App\Controller\ClassNamePrefix1',
                            'App\Admin\Controller\ClassNamePrefix2',
                        ],
                        'file_tree_depths' => [
                            'App\Controller\ClassNamePrefix1\SubFolder1' => 2,
                            'App\Controller\ClassNamePrefix1\SubFolder2\SubFolder3' => 3,
                        ],
                    ],
                    'bar' => [
                        'encore_build_name' => 'bar',
                        'entry_name_prefix' => 'bar',
                        'package_name' => 'foo',
                        'controller_class_name_prefixes' => [
                            'App\Controller\ClassNamePrefix3',
                            'App\Admin\Controller\ClassNamePrefix4',
                        ],
                        'file_tree_depths' => [
                            'App\Controller\ClassNamePrefix5\SubFolder4' => 2,
                            'App\Controller\ClassNamePrefix5\SubFolder5\SubFolder6' => 3,
                        ],
                    ],
                ]
            ],
        ];
    }

    protected function createContainerByConfigFile($filename, array $data = [])
    {
        $container = $this->createBaseContainer($data);

        $container->registerExtension(new NadiaWebpackEncoreExtraExtension());

        $this->loadConfigFile($container, $filename);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container->compile();

        return $container;
    }

    /**
     * @param array $data
     *
     * @return ContainerBuilder
     */
    protected function createBaseContainer(array $data = [])
    {
        // Make sure cache directory is different
        sleep(1);

        return new ContainerBuilder(new ParameterBag(array_merge([
            'kernel.bundles' => [
                'NadiaWebpackEncoreExtraBundle' =>
                    'Nadia\\Bundle\\NadiaWebpackEncoreExtraBundle\\NadiaWebpackEncoreExtraBundle',
            ],
            'kernel.bundles_metadata' => [
                'NadiaWebpackEncoreExtraBundle' => [
                    'namespace' => 'Nadia\\Bundle\\NadiaWebpackEncoreExtraBundle',
                    'path' => __DIR__ . '/../..',
                ],
            ],
            'kernel.cache_dir' => sys_get_temp_dir() . '/nadia-webpack-encore-extra-bundle-tests-' . time(),
            'kernel.project_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.container_class' => 'testContainer',
        ], $data)));
    }
}
