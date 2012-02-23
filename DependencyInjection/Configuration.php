<?php

namespace Knp\Bundle\TranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('knplabs_translator');

        $rootNode
            ->children()
                ->booleanNode('include_vendor_assets')->defaultTrue()->end()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('roles')
                    ->defaultNull()
                    ->beforeNormalization()->ifString()->then(function($v) {
                        return preg_split('/\s*,\s*/', $v);
                    })->end()
                    ->prototype('scalar')->end()
                ->end()
        ;

        return $treeBuilder;
    }
}

