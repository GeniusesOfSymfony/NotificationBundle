<?php

namespace Gos\Bundle\NotificationBundle\Publisher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Generator\GeneratorInterface;
use Predis\Client;
use Psr\Log\LoggerInterface;

class RedisPublisher implements PublisherInterface
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var GeneratorInterface
     */
    protected $routeGenerator;

    /**
     * @param GeneratorInterface $routeGenerator
     * @param Client             $redis
     * @param LoggerInterface    $logger
     */
    public function __construct(GeneratorInterface $routeGenerator, Client $redis, LoggerInterface $logger = null)
    {
        $this->routeGenerator = $routeGenerator;
        $this->redis = $redis;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function publish($routeName, array $routeParameters = [], NotificationInterface $notification, NotificationContextInterface $context = null)
    {
        $channel = $this->routeGenerator->generate($routeName, $routeParameters);

        if (null !== $this->logger) {
            $this->logger->info(sprintf(
                'push %s into %s',
                $notification->getTitle(),
                $channel
            ), $notification->toArray());
        }

        $data = [];
        $data['notification'] = $notification;

        if (null !== $context) {
            $data['context'] = $context;
        }

        $message = json_encode($data);

        $this->redis->publish($channel, $message);
    }
}
