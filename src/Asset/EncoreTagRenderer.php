<?php

/**
 * This file is part of the NadiaWebpackEncoreExtraBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Exception\EntrypointNotFoundException;

/**
 * Class EncoreTagRenderer
 */
class EncoreTagRenderer
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TagRenderer
     */
    private $tagRenderer;

    /**
     * @var array
     */
    private $builds;

    const DEFAULT_FILE_TREE_DEPTH = 1;

    /**
     * EncoreTagRenderer constructor.
     *
     * @param RequestStack  $requestStack
     * @param TagRenderer   $tagRenderer
     * @param array         $builds
     * Format:
     * <code>
     * [
     *   "$buildName1" => [
     *     'encore_build_name' => "$buildName1",
     *     'entry_name_prefix' => '',
     *     'package_name' => '',
     *     'controller_class_name_prefixes' => [
     *       'App\Controller\ClassNamePrefix1',
     *       'App\Admin\Controller\ClassNamePrefix2',
     *       ...
     *     ],
     *     'file_tree_depths' => [
     *       'App\Controller\ClassNamePrefix1\SubFolder1' => 2,
     *       'App\Controller\ClassNamePrefix1\SubFolder2\SubFolder3' => 3,
     *     ]
     *   ],
     *   "$buildName2" => [],
     *   ...
     * ]
     * </code>
     */
    public function __construct(RequestStack $requestStack, TagRenderer $tagRenderer, array $builds)
    {
        $this->requestStack = $requestStack;
        $this->tagRenderer = $tagRenderer;
        $this->builds = $builds;
    }

    /**
     * Render webpack script tags
     *
     * If our entry name convention gives us an empty entry name, we will try to use $defaultEntryName
     *
     * @param string $defaultEntryName
     *
     * @return string
     */
    public function renderWebpackScriptTags($defaultEntryName = '')
    {
        $info = $this->getWebpackEntryInfo();

        if (!empty($info) && !empty($info['entry_name'])) {
            try {
                return $this->tagRenderer->renderWebpackScriptTags(
                    $info['entry_name'],
                    $info['package_name'],
                    $info['entry_point_name']
                );
            } catch (EntrypointNotFoundException $e) {
            }

            if (!empty($defaultEntryName)) {
                return $this->tagRenderer->renderWebpackScriptTags(
                    $defaultEntryName,
                    $info['package_name'],
                    $info['entry_point_name']
                );
            }
        }

        return '';
    }

    /**
     * Render webpack link tags
     *
     * If our entry name convention gives us an empty entry name, we will try to use $defaultEntryName
     *
     * @param string|null $defaultEntryName
     *
     * @return string
     */
    public function renderWebpackLinkTags($defaultEntryName)
    {
        $info = $this->getWebpackEntryInfo();

        if (!empty($info) && !empty($info['entry_name'])) {
            try {
                return $this->tagRenderer->renderWebpackLinkTags(
                    $info['entry_name'],
                    $info['package_name'],
                    $info['entry_point_name']
                );
            } catch (EntrypointNotFoundException $e) {
            }

            if (!empty($defaultEntryName)) {
                return $this->tagRenderer->renderWebpackLinkTags(
                    $defaultEntryName,
                    $info['package_name'],
                    $info['entry_point_name']
                );
            }
        }

        return '';
    }

    /**
     * @return array
     */
    private function getWebpackEntryInfo()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return [];
        }

        $controller = $request->attributes->get('_controller');
        $build = $this->getBuild($controller);

        if (empty($build)) {
            return [];
        }

        $routeName = $request->attributes->get('_route');
        $entryName = $this->generateWebpackEntryName($controller, $routeName, $build);

        return [
            'entry_name' => $entryName,
            'package_name' => empty($build['package_name']) ? null : $build['package_name'],
            'entry_point_name' => $build['encore_build_name'],
        ];
    }

    /**
     * Get webpack entry name with Request information (route name and controller name)
     *
     * @param string $controller
     * @param string $routeName
     * @param array $build
     *
     * @return string
     */
    private function generateWebpackEntryName($controller, $routeName, array $build)
    {
        $entryNamePrefix = $build['entry_name_prefix'];
        $controllerSuffix = str_replace($build['controller_class_name_prefix'], '', $controller);
        $controllerSuffix = trim($controllerSuffix, '\\ ');
        $parts = explode('\\', $controllerSuffix);

        if (count($parts) <= self::DEFAULT_FILE_TREE_DEPTH) {
            return $entryNamePrefix . $routeName;
        }

        $segments = [];

        for ($i = 0; $i < $build['file_tree_depth'] && isset($parts[$i]); ++$i) {
            $segments[] = $this->camelCaseToHyphenCase($parts[$i]);
        }

        return $build['entry_name_prefix'] . implode('/', $segments) . '/' . $routeName;
    }

    /**
     * Get encore build configuration
     *
     * @param string $controller Controller class name
     *
     * @return array
     */
    private function getBuild($controller)
    {
        foreach ($this->builds as $build) {
            foreach ($build['controller_class_name_prefixes'] as $controllerClassNamePrefix) {
                if (0 === strpos($controller, $controllerClassNamePrefix)) {
                    $build['controller_class_name_prefix'] = $controllerClassNamePrefix;
                    $build['file_tree_depth'] = $this->getFireTreeDepth($controller, $build);

                    return $build;
                }
            }
        }

        return [];
    }

    /**
     * @param string $controller
     * @param array $build
     *
     * @return int
     */
    private function getFireTreeDepth($controller, array $build)
    {
        foreach ($build['file_tree_depths'] as $controllerClassNamePrefix => $depth) {
            if (0 === strpos($controller, $controllerClassNamePrefix)) {
                return $depth;
            }
        }

        return self::DEFAULT_FILE_TREE_DEPTH;
    }

    /**
     * Convert "CamelCaseString" to "camel-case-string" (hyphen case string)
     *
     * @param string $input
     *
     * @return string
     */
    private function camelCaseToHyphenCase($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }
}
