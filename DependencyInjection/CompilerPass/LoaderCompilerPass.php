<?php

namespace Gos\Bundle\NotificationBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class LoaderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('gos_notification.loader.resolver');

        $taggedServices = $container->findTaggedServiceIds('gos_notification.loader');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}
