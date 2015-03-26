<?php

namespace Gos\Bundle\NotificationBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class PusherCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('gos_notification.pusher.registry');
        $taggedServices = $container->findTaggedServiceIds('gos_notification.pusher');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addPusher', [new Reference($id)]);
        }
    }
}
