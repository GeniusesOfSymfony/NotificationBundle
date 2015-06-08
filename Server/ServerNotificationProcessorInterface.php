<?php

namespace Gos\Bundle\NotificationBundle\Server;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use React\EventLoop\LoopInterface;

interface ServerNotificationProcessorInterface
{
    /**
     * @param PusherInterface[]            $pushers
     * @param RouteInterface               $route
     * @param PubSubRequest                $request
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     *
     * @throws \Exception
     */
    public function push(
        $pushers,
        RouteInterface $route,
        PubSubRequest $request,
        MessageInterface $message,
        NotificationInterface $notification,
        NotificationContextInterface $context
    );

    /**
     * @param MessageInterface $message
     *
     * @return array
     */
    public function getNotification(MessageInterface $message);

    /**
     * @param MessageInterface $message
     *
     * @return PubSubRequest
     */
    public function getRequest(MessageInterface $message);

    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop);

    /**
     * @param MessageInterface $message
     *
     * @throws \Exception
     */
    public function __invoke(MessageInterface $message);
}
