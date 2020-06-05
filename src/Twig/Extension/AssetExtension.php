<?php

/**
 * This file is part of the NadiaWebpackEncoreExtraBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Twig\Extension;

use JMS\Serializer\Serializer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Nadia\Bundle\NadiaWebpackEncoreExtraBundle\Asset\EncoreTagRenderer;

/**
 * Class AssetExtension
 */
class AssetExtension extends AbstractExtension
{
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var EncoreTagRenderer
     */
    protected $encoreTagRenderer;

    /**
     * AssetExtension constructor.
     *
     * @param Serializer        $serializer
     * @param EncoreTagRenderer $encoreTagRenderer
     */
    public function __construct(Serializer $serializer, EncoreTagRenderer $encoreTagRenderer)
    {
        $this->serializer = $serializer;
        $this->encoreTagRenderer = $encoreTagRenderer;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'encore_extra_render_php_data_as_javascript',
                [$this, 'renderPhpDataAsJavascript'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'encore_extra_render_script_tags',
                [$this, 'renderWebpackScriptTags'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'encore_extra_render_css_tags',
                [$this, 'renderWebpackLinkTags'],
                ['is_safe' => ['html']]
            ),
        );
    }

    /**
     * Render javascript with PHP data
     *
     * @param array  $data                PHP data content
     * @param string $phpDataVariableName JavaScript variable name (default is "PhpData")
     *
     * @return string
     */
    public function renderPhpDataAsJavascript(array $data = [], $phpDataVariableName = 'PhpData')
    {
        $data = (object) $data;
        $object = $this->serializer->serialize($data, 'json');
        $variableName = json_encode($phpDataVariableName);

        return <<<JS
<script type="text/javascript">
  window[{$variableName}] = {$object};
</script>
JS;
    }

    /**
     * Render webpack script tags
     *
     * @param string $defaultEntryName
     *
     * @return string
     */
    public function renderWebpackScriptTags($defaultEntryName = '')
    {
        return $this->encoreTagRenderer->renderWebpackScriptTags($defaultEntryName);
    }

    /**
     * Render webpack link tags
     *
     * @param string $defaultEntryName
     *
     * @return string
     */
    public function renderWebpackLinkTags($defaultEntryName = '')
    {
        return $this->encoreTagRenderer->renderWebpackLinkTags($defaultEntryName);
    }
}
