<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 01:45.
 */

namespace Umbrella\AdminBundle\Extension;

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
            new TwigFunction('admin_theme_name', array($this, 'getThemeName')),
            new TwigFunction('admin_config', array($this, 'getConfigValue')),
        );
    }

    /**
     * @return mixed|null
     */
    public function getThemeName()
    {
        return $this->configService->getValue('theme.name');
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getConfigValue($key , $default = null)
    {
        return $this->configService->getValue($key, $default);
    }
}