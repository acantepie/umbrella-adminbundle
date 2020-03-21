<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 07/05/17
 * Time: 13:17.
 */

namespace Umbrella\AdminBundle\Menu;

use Symfony\Component\Yaml\Yaml;
use Umbrella\CoreBundle\Component\Menu\MenuBuilder;
use Umbrella\CoreBundle\Component\Menu\Model\Menu;
use Umbrella\CoreBundle\Component\Menu\Model\MenuNode;

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
     * @param MenuBuilder $builder
     * @return Menu
     */
    public function createMenu(MenuBuilder $builder)
    {
        $root = $builder->createRootNode();

        if (!empty($this->yml_path && file_exists($this->yml_path))) {
            $data = Yaml::parse(file_get_contents($this->yml_path));
            if (is_array($data)) {
                foreach ($data as $name => $data) {
                    if (array_key_exists('action', $data)) {
                        $root->addChild($name, $this->parsePageNode($builder, $data));
                    } else {
                        $root->addChild($name, $this->parseHeaderNode($builder, $data));
                    }
                }
            }
        }

        return $builder->getMenu();
    }

    /**
     * @param MenuBuilder $builder
     * @param array $data
     *
     * @return MenuNode
     */
    protected function parseHeaderNode(MenuBuilder $builder, array $data)
    {
        $node = $builder->createHeaderNode($data);

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $name => $dataChild) {
                $node->addChild($name, $this->parsePageNode($builder, $dataChild));
            }
        }

        return $node;
    }

    /**
     * @param MenuBuilder $builder
     * @param array $data
     *
     * @return MenuNode
     */
    protected function parsePageNode(MenuBuilder $builder, array $data)
    {
        $node = $builder->createPageNode($data);

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $name => $dataChild) {
                $node->addChild($name, $this->parsePageNode($builder, $dataChild));
            }
        }

        return $node;
    }
}
