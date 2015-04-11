<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;

interface PusherInterface
{
    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param MessageInterface                  $message
     * @param NotificationInterface             $notification
     * @param PubSubRequest                     $request
     * @param NotificationContextInterface|null $context
     *
     * @return mixed
     */
    public function push(MessageInterface $message, NotificationInterface $notification, PubSubRequest $request, NotificationContextInterface $context = null);
}
