<?php

namespace Umbrella\AdminBundle\DependencyInjection;

use Umbrella\AdminBundle\Form\UserType;
use Umbrella\AdminBundle\Form\ProfileType;
use Umbrella\AdminBundle\Form\UserGroupType;
use Umbrella\AdminBundle\DataTable\UserTableType;
use Umbrella\AdminBundle\DataTable\UserGroupTableType;
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
            ->append($this->assetsNode())
            ->children()
                ->arrayNode('user')->addDefaultsIfNotSet()
                ->append($this->userCrudNode())
                ->append($this->groupCrudNode())
                ->append($this->profileCrudNode())
                ->append($this->mailNode());

        return $treeBuilder;
    }

    private function menuNode()
    {
        $treeBuilder = new TreeBuilder('menu');
        $themeNode = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $themeNode->children()
            ->scalarNode('file')
                ->defaultNull()
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

    private function userCrudNode()
    {
        $treeBuilder = new TreeBuilder('user_crud');
        $node = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $node->children()
            ->scalarNode('class')
            ->defaultValue('App\\Entity\\User')
            ->end()
            ->scalarNode('table')
            ->defaultValue(UserTableType::class)
            ->end()
            ->scalarNode('form')
            ->defaultValue(UserType::class)
            ->end();
        return $node;
    }

    private function groupCrudNode()
    {
        $treeBuilder = new TreeBuilder('group_crud');
        $node = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $node->children()
            ->scalarNode('class')
            ->defaultValue('App\\Entity\\UserGroup')
            ->end()
            ->scalarNode('table')
            ->defaultValue(UserGroupTableType::class)
            ->end()
            ->scalarNode('form')
            ->defaultValue(UserGroupType::class)
            ->end();
        return $node;
    }

    private function profileCrudNode()
    {
        $treeBuilder = new TreeBuilder('profile_crud');
        $node = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $node->children()
            ->scalarNode('form')
            ->defaultValue(ProfileType::class)
            ->end();
        return $node;
    }

    private function mailNode()
    {
        $treeBuilder = new TreeBuilder('mail');
        $node = $treeBuilder->getRootNode()->addDefaultsIfNotSet();
        $node->children()
            ->scalarNode('from_email')
            ->defaultValue('no-reply@umbrella.dev')
            ->end()
            ->scalarNode('from_name')
            ->defaultValue(null)
            ->end();
        return $node;
    }
}
