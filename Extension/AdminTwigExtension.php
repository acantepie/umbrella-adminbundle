<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 01:45.
 */

namespace Umbrella\AdminBundle\Extension;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AdminTwigExtension.
 */
class AdminTwigExtension extends AbstractExtension
{
    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * AdminTwigExtension constructor.
     *
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('admin_theme_name', [$this, 'themeName']),
            new TwigFunction('admin_script_entry', [$this, 'scriptEntry']),
            new TwigFunction('admin_stylesheet_entry', [$this, 'stylesheetEntry']),
            new TwigFunction('admin_route_profile', [$this, 'routeProfile']),
            new TwigFunction('admin_route_logout', [$this, 'routeLogout']),
            new TwigFunction('admin_notification_enable', [$this, 'fileWriterNotificationEnable']),
        ];
    }

    /**
     * @return mixed
     */
    public function fileWriterNotificationEnable()
    {
        return $this->parameters->get('umbrella_admin.filewriter.notification_enable');
    }

    /**
     * @return mixed
     */
    public function themeName()
    {
        return $this->parameters->get('umbrella_admin.theme.name');
    }

    /**
     * @return mixed
     */
    public function scriptEntry()
    {
        return $this->parameters->get('umbrella_admin.assets.script_entry');
    }

    /**
     * @return mixed
     */
    public function stylesheetEntry()
    {
        return $this->parameters->get('umbrella_admin.assets.stylesheet_entry');
    }

    /**
     * @return mixed
     */
    public function routeProfile()
    {
        return $this->parameters->get('umbrella_admin.route.profile');
    }

    /**
     * @return mixed
     */
    public function routeLogout()
    {
        return $this->parameters->get('umbrella_admin.route.logout');
    }
}
