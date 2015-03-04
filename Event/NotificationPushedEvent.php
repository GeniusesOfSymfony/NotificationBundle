<?php

namespace Gos\Bundle\NotificationBundle\Event;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Pusher\PusherInterface;
use Symfony\Component\EventDispatcher\Event;

class NotificationPushedEvent extends Event
{
    /**
     * @var MessageInterface
     */
    protected $message;

    /**
     * @var NotificationInterface
     */
    protected $notification;

    /**
     * @var NotificationContextInterface
     */
    protected $context;

    /**
     * @var PusherInterface
     */
    protected $pusher;

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     * @param PusherInterface              $pusher
     */
    public function __construct(
        MessageInterface $message,
        NotificationInterface $notification,
        NotificationContextInterface $context,
        PusherInterface $pusher
    ) {
        $this->message = $message;
        $this->notification = $notification;
        $this->context = $context;
        $this->pusher = $pusher;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return NotificationContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return PusherInterface
     */
    public function getPusher()
    {
        return $this->pusher;
    }
}
