<?php

namespace Gos\Bundle\NotificationBundle\Event;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class NotificationPublishedEvent extends Event
{
    /**
     * @var NotificationInterface
     */
    protected $notification;

    /**
     * @var NotificationContextInterface
     */
    protected $context;

    /**
     * @var MessageInterface
     */
    protected $message;

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     */
    public function __construct(
        MessageInterface $message,
        NotificationInterface $notification,
        NotificationContextInterface $context
    ) {
        $this->message = $message;
        $this->notification = $notification;
        $this->context = $context;
    }

    /**
     * @return NotificationContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }
}
