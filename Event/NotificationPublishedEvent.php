<?php

namespace Gos\Bundle\NotificationBundle\Event;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
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
     * @var NotificationContextInterface|null
     */
    protected $context;

    /**
     * @var MessageInterface
     */
    protected $message;

    /** @var  PubSubRequest */
    protected $request;

    /**
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface|null $context
     * @param PubSubRequest                $request
     */
    public function __construct(
        MessageInterface $message,
        NotificationInterface $notification,
        NotificationContextInterface $context = null,
        PubSubRequest $request
    ) {
        $this->message = $message;
        $this->notification = $notification;
        $this->context = $context;
        $this->request = $request;
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

    /**
     * @return PubSubRequest
     */
    public function getRequest()
    {
        return $this->request;
    }
}
