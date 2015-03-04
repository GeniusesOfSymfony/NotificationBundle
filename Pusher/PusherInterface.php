<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface PusherInterface
{
    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     */
    public function push(MessageInterface $message, NotificationInterface $notification, NotificationContextInterface $context);

    /**
     * @return array
     */
    public function getChannelsListened();
}
