<?php

namespace Gos\Bundle\NotificationBundle;

use Gos\Bundle\NotificationBundle\DependencyInjection\CompilerPass\LoaderCompilerPass;
use Gos\Bundle\NotificationBundle\DependencyInjection\CompilerPass\PusherCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class GosNotificationBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PusherCompilerPass());
        $container->addCompilerPass(new LoaderCompilerPass());
    }
}
