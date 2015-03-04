<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Event\NotificationEvents;
use Gos\Bundle\NotificationBundle\Event\NotificationPushedEvent;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Predis\Async\Client;
use React\EventLoop\LoopInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RedisPusher.
 */
class RedisPusher implements PusherInterface, PusherLoopAwareInterface
{
    const ALIAS = 'gos_redis';

    /**
     * @var string
     */
    protected $serverHost;

    /**
     * @var string|int
     */
    protected $serverPort;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param string                   $serverHost
     * @param string                   $serverPort
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct($serverHost, $serverPort, EventDispatcherInterface $eventDispatcher)
    {
        $this->serverHost = $serverHost;
        $this->serverPort = $serverPort;
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
        $notifier = new Client('tcp://' . $this->serverHost . ':' . $this->serverPort, $this->loop);

        $notifier->lpush((string) $message->getChannel(), json_encode($notification), function () use ($message, $notification, $context) {
            $this->eventDispatcher->dispatch(NotificationEvents::NOTIFICATION_PUSHED, new NotificationPushedEvent($message, $notification, $context, $this));
        });
    }

    /**
     * @return array
     */
    public function getChannelsListened()
    {
        return array(
            'psubscribe' => array('notification:user:*', 'notification:application:*'),
        );
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
