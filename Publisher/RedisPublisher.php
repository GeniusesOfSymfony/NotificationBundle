<?php

namespace Gos\Bundle\NotificationBundle\Publisher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
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
     * @param Client          $redis
     * @param LoggerInterface $logger
     */
    public function __construct(Client $redis, LoggerInterface $logger = null)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function publish($channel, NotificationInterface $notification, NotificationContextInterface $context)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf(
                'push %s into %s',
                $notification->getTitle(),
                $channel
            ), $notification->toArray());
        }

        $message = json_encode(array($notification, $context));

        $this->redis->publish($channel, $message);
    }
}
