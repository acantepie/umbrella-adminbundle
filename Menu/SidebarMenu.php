<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 07/05/17
 * Time: 13:17.
 */

namespace Umbrella\AdminBundle\Menu;

use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Umbrella\CoreBundle\Component\Menu\MenuFactory;
use Umbrella\CoreBundle\Component\Menu\Model\Breadcrumb;
use Umbrella\CoreBundle\Component\Menu\Model\Menu;
use Umbrella\CoreBundle\Component\Menu\Model\MenuItem;

/**
 * Class SidebarMenu.
 */
class SidebarMenu
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var string
     */
    private $ymlPath;

    /**
     * SidebarMenu constructor.
     *
     * @param Environment $twig
     * @param null $ymlPath
     */
    public function __construct(Environment $twig, $ymlPath = null)
    {
        $this->twig = $twig;
        $this->ymlPath = $ymlPath;
    }

    /**
     * @param MenuFactory $factory
     * @return Menu
     */
    public function createMenu(MenuFactory $factory)
    {
        if (!file_exists($this->ymlPath)) {
            throw new \RuntimeException(sprintf("Can't load menu, resource %s doesn't exist", $this->ymlPath));
        }
        $data = (array) Yaml::parse(file_get_contents($this->ymlPath));

        $menu = $factory->createMenu();
        foreach ($data as $id => $childOptions) {
            $menu->getRoot()->addChild($id, $childOptions);
        }
        return $menu;
    }

    /**
     * @param Menu $menu
     * @return string
     */
    public function renderMenu(Menu $menu)
    {
        return $this->twig->render('@UmbrellaAdmin/Menu/sidebar.html.twig', [
            'menu' => $menu
        ]);
    }

    /**
     * @param Breadcrumb $breadcrumb
     * @return string
     */
    public function renderBreadcrumb(Breadcrumb $breadcrumb)
    {
        return $this->twig->render('@UmbrellaAdmin/Menu/breadcrumb.html.twig', [
            'breadcrumb' => $breadcrumb
        ]);
    }
}
