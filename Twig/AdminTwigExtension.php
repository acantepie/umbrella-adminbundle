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
            new TwigFunction('stylesheet_entry', array($this, 'getStylesheetEntry')),
            new TwigFunction('script_entry', array($this, 'getScriptEntry')),

        );
    }

    public function getName()
    {
        return $this->configService->getValue('theme.name');
    }

    public function getStylesheetEntry()
    {
        return $this->configService->getValue('assets.stylesheet_entry');
    }

    public function getScriptEntry()
    {
        return $this->configService->getValue('assets.script_entry');
    }
}