<?php
namespace Gos\Bundle\NotificationBundle\Publisher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface PublisherInterface
{
    /**
     * @param string                       $channel
     * @param NotificationInterface        $notification
     * @param NotificationContextInterface $context
     */
    public function publish($channel, NotificationInterface $notification, NotificationContextInterface $context);
}
