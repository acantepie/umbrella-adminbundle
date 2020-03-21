<?php

namespace Umbrella\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('umbrella_admin');
        $rootNode->append($this->menuNode());
        $rootNode->append($this->themeNode());
        $rootNode->append($this->assetsNode());
        return $treeBuilder;
    }

    private function menuNode()
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $themeNode */
        $themeNode = $treeBuilder->root('menu')->addDefaultsIfNotSet();
        $themeNode->children()
            ->scalarNode('sitemap')
                ->defaultNull()
                ->end()
            ->scalarNode('css_class')
                ->defaultValue('dark dk')
                ->end();

        return $themeNode;
    }

    private function themeNode()
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $themeNode */
        $themeNode = $treeBuilder->root('theme')->addDefaultsIfNotSet();
        $themeNode->children()
            ->scalarNode('name')
                ->defaultValue('Umbrella')
                ->end()
            ->scalarNode('logo')
                ->defaultValue('')
                ->end();
        return $themeNode;
    }

    private function assetsNode()
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $themeNode */
        $assetNode = $treeBuilder->root('assets')->addDefaultsIfNotSet();
        $assetNode->children()
            ->scalarNode('stylesheet_entry')
            ->defaultValue('/build/umbrella_admin.css')
            ->end()
            ->scalarNode('script_entry')
            ->defaultValue('/build/umbrella_admin.js')
            ->end();
        return $assetNode;
    }
}
