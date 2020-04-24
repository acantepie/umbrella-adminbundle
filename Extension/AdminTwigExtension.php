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
        return array(
            new TwigFunction('admin_theme_name', array($this, 'themeName')),
            new TwigFunction('admin_script_entry', array($this, 'scriptEntry')),
            new TwigFunction('admin_stylesheet_entry', array($this, 'stylesheetEntry')),
            new TwigFunction('admin_route_profile', array($this, 'routeProfile')),
            new TwigFunction('admin_route_logout', array($this, 'routeLogout')),
        );
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