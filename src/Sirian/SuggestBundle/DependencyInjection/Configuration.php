<?php

namespace Sirian\SuggestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sirian_suggest');

        $this->addConfiguration($rootNode, 'odm');
        $this->addConfiguration($rootNode, 'orm');

        $rootNode
            ->children()
                ->arrayNode('custom')
                ->useAttributeAsKey('id')
                ->prototype('array')
                    ->beforeNormalization()->ifString()->then(function ($value) {
                        return [
                            'suggester' => $value
                        ];
                    })->end()
                    ->children()
                        ->scalarNode('suggester')->end()
                        ->arrayNode('form_options')
                            ->prototype('variable')
                        ->end()

        ;

        return $treeBuilder;
    }

    public function addConfiguration(ArrayNodeDefinition $rootNode, $name)
    {
        $rootNode
            ->children()
                ->arrayNode($name)
                ->useAttributeAsKey('id')
                ->prototype('array')
                    ->children()
                        ->scalarNode('class')->isRequired()->end()
                        ->scalarNode('id_property')->defaultValue('id')->end()
                        ->scalarNode('property')->isRequired()->end()
                        ->arrayNode('search')
                            ->prototype('scalar')
                            ->treatNullLike('middle')
                                ->validate()
                                    ->ifNotInArray(['prefix', 'suffix', 'middle'])
                                    ->thenInvalid('Available search types: "prefix", "suffix", "middle"')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_options')
                            ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
