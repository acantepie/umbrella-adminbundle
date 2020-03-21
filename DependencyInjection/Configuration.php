<?php

namespace Umbrella\AdminBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('umbrella_admin');
        $treeBuilder
            ->getRootNode()
            ->append($this->menuNode())
            ->append($this->themeNode())
            ->append($this->assetsNode());
        return $treeBuilder;
    }

    private function menuNode()
    {
        $treeBuilder = new TreeBuilder('menu');
        $themeNode = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
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
        $treeBuilder = new TreeBuilder('theme');
        $themeNode = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
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
        $treeBuilder = new TreeBuilder('assets');
        $assetNode = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
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
