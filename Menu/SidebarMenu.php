<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 07/05/17
 * Time: 13:17.
 */

namespace Umbrella\AdminBundle\Menu;

use Symfony\Component\Yaml\Yaml;
use Umbrella\CoreBundle\Component\Menu\Model\Menu;
use Umbrella\CoreBundle\Component\Menu\MenuBuilder;

/**
 * Class SidebarMenu.
 */
class SidebarMenu
{

    /**
     * @var string
     */
    private $yml_path;

    /**
     * SidebarMenu constructor.
     * @param $yml_path
     */
    public function __construct($yml_path = null)
    {
        $this->yml_path = $yml_path;
    }

    /**
     * @param  MenuBuilder $builder
     * @return Menu
     */
    public function createMenu(MenuBuilder $builder)
    {
        if (!file_exists($this->yml_path)) {
            throw new \RuntimeException(sprintf("Can't load menu, resource %s doesn't exist", $this->yml_path));
        }

        $data = (array) Yaml::parse(file_get_contents($this->yml_path));
        foreach ($data as $id => $options) {
            $builder->addNode($id, $options);
        }

        return $builder->getMenu();
    }
}
