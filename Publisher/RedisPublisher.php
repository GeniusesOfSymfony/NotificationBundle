<?php

namespace Gos\Bundle\NotificationBundle\Publisher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function publish($channel, NotificationInterface $notification, NotificationContextInterface $context = null)
    {
        $this->logger->info(sprintf(
            'push %s into %s',
            $notification->getTitle(),
            $channel
        ), $notification->toArray());

        $data = [];
        $data['notification'] = $notification;

        if (null !== $context) {
            $data['context'] = $context;
        }

        $message = json_encode($data);

        $this->redis->publish($channel, $message);
    }
}
