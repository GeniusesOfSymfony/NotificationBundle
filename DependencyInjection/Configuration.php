<?php

namespace Gos\Bundle\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gos_notification');

        $rootNode->children()
            ->arrayNode('pusher')
                ->prototype('scalar')
                ->end()
            ->end()
            ->scalarNode('fetcher')
                ->cannotBeEmpty()
                ->isRequired()
            ->end()
            ->scalarNode('publisher')
                ->cannotBeEmpty()
                ->isRequired()
            ->end()
            ->arrayNode('class')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('notification')
                        ->defaultValue('Gos\Bundle\NotificationBundle\Model\Notification')
                    ->end()
                    ->scalarNode('notification_context')
                        ->defaultValue('Gos\Bundle\NotificationBundle\Context\NotificationContext')
                    ->end()
                ->end()
            ->end()
            ->arrayNode('pubsub_server')
                ->children()
                    ->scalarNode('type')->end()
                    ->arrayNode('config')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
