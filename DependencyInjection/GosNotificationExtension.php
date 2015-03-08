<?php

namespace Gos\Bundle\NotificationBundle\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class GosNotificationExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/services')
        );

        $loader->load('services.yml');

        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter('gos_notification.pubsub_server.type', $configs['pubsub_server']['type']);
        $container->setParameter('gos_notification.pubsub_server.config', $configs['pubsub_server']['config']);

        //class
        $container->setParameter('gos_notification.notification_class', $configs['class']['notification']);
        $container->setParameter('gos_notification.notification_context_class', $configs['class']['notification_context']);

        //pusher
        if (isset($configs['pusher']) && !empty($configs['pusher'])) {
            $pusherRegistryDef = $container->getDefinition('gos_notification.pusher.registry');

            foreach ($configs['pusher'] as $pusher) {
                $pusherRegistryDef->addMethodCall('addPusher', array(new Reference(ltrim($pusher, '@'))));
            }
        }

        //fetcher
        $container->setAlias('gos_notification.fetcher', ltrim($configs['fetcher'], '@'));

        //publisher
        $container->setAlias('gos_notification.publisher', ltrim($configs['publisher'], '@'));
    }

    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['MonologBundle'])) {
            $monologConfig = array(
                'channels' => array('notification'),
                'handlers' => array(
                    'notification' => array(
                        'type' => 'stream',
                        'path' => '%kernel.logs_dir%/notification.log',
                        'channels' => 'notification',
                    ),
                    'notification_cli' => array(
                        'type' => 'console',
                        'verbosity_levels' => array(
                            'VERBOSITY_NORMAL' => Logger::INFO,
                        ),
                        'channels' => 'notification',
                    ),
                ),
            );

            $container->prependExtensionConfig('monolog', $monologConfig);
        }
    }
}
