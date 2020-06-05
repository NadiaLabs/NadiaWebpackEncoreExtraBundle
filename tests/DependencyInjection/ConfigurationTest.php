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

use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);
        $expectConfig = [
            'builds' => [],
        ];

        $this->assertEquals($expectConfig, $config);
    }

    /**
     * @param array $testConfig
     * @param array $expectedConfig
     *
     * @dataProvider configDataProvider
     */
    public function testDefaultBuild(array $testConfig, array $expectedConfig)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$testConfig]);

        $this->assertEquals($expectedConfig, $config);
    }

    public function configDataProvider()
    {
        return [
            // Case 1
            [
                ['default_build' => null],
                [
                    'default_build' => [
                        'entry_name_prefix' => '',
                        'package_name' => '',
                        'controller_class_name_prefixes' => [],
                        'file_tree_depths' => [],
                    ],
                    'builds' => [],
                ]
            ],
            // Case 2
            [
                [
                    'default_build' => [
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
                    ]
                ],
                [
                    'default_build' => [
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
                    'builds' => [],
                ]
            ],
            // Case 3
            [
                // $testConfig
                [
                    'builds' => [
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
                // $expectedConfig
                [
                    'builds' => [
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
                    ],
                ]
            ]
        ];
    }
}
