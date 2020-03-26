<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 14/05/17
 * Time: 11:45.
 */

namespace Umbrella\AdminBundle\Menu;

use Twig\Environment;
use Umbrella\CoreBundle\Component\Menu\Model\Menu;
use Umbrella\CoreBundle\Component\Menu\Renderer\MenuRendererInterface;

/**
 * Class SideBarMenuRenderer.
 */
class SideBarMenuRenderer implements MenuRendererInterface
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * SideMenuRenderer constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Menu $menu
     * @return string
     */
    public function render(Menu $menu)
    {
        return $this->twig->render('@UmbrellaAdmin/Menu/sidebar.html.twig', array(
            'menu' => $menu
        ));
    }
}
