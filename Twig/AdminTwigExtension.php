<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 01:45.
 */

namespace Umbrella\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Umbrella\AdminBundle\Services\AdminConfigService;

/**
 * Class AdminTwigExtension.
 */
class AdminTwigExtension extends AbstractExtension
{

    /**
     * @var AdminConfigService
     */
    private $configService;

    /**
     * ThemeTwigExtension constructor.
     * @param AdminConfigService $configService
     */
    public function __construct(AdminConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('theme_name', array($this, 'getName')),
            new TwigFunction('theme_logo', array($this, 'getLogo')),
            new TwigFunction('sidebar_css_class', array($this, 'getSidebarCssClass')),
            new TwigFunction('stylesheet_entry', array($this, 'getStylesheetEntry')),
            new TwigFunction('script_entry', array($this, 'getScriptEntry')),
            new TwigFunction('render_icon', array($this, 'renderIcon'), array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return $this->configService->getValue('theme.name');
    }

    public function getLogo()
    {
        return $this->configService->getValue('theme.logo');
    }

    public function getStylesheetEntry()
    {
        return $this->configService->getValue('assets.stylesheet_entry');
    }

    public function getScriptEntry()
    {
        return $this->configService->getValue('assets.script_entry');
    }

    public function getSidebarCssClass()
    {
        return $this->configService->getValue('menu.css_class');
    }

    /**
     * @param string $icon
     * @param string $class
     * @return string
     */
    public function renderIcon($icon, $class = '')
    {
        if (preg_match('/fa-/', $icon)) {
            $icon = preg_replace('/fa\ |fa$/', '', $icon);
            return "<i class='fa fa-fw $icon $class'></i>";
        }

        // material icon
        return "<i class='material-icons $class'>$icon</i>";
    }

}