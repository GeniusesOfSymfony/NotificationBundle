<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Event\NotificationEvents;
use Gos\Bundle\NotificationBundle\Event\NotificationPushedEvent;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Predis\Client;
use React\EventLoop\LoopInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RedisPusher.
 */
class RedisPusher implements PusherInterface, PusherLoopAwareInterface
{
    const ALIAS = 'gos_redis';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param Client                   $client
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Client $client, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     */
    public function push(MessageInterface $message, NotificationInterface $notification, NotificationContextInterface $context)
    {
        if (false !== strpos($message->getChannel(), 'all')) {
        } else {
            $pipe = $this->client->pipeline();
            $pipe->lpush($message->getChannel(), json_encode($notification->toArray()));
            $pipe->incr($message->getChannel() . '-counter');
            $pipe->execute();

            $this->eventDispatcher->dispatch(NotificationEvents::NOTIFICATION_PUSHED, new NotificationPushedEvent($message, $notification, $context, $this));
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
