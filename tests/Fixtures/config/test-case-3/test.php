<?php

$container->loadFromExtension(
    'nadia_webpack_encore_extra',
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
    ]
);
