<?php

namespace Gos\Bundle\NotificationBundle\Publisher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface PublisherInterface
{
    /**
     * @param string                            $routeName
     * @param string[]                          $routeParameters
     * @param NotificationInterface             $notification
     * @param NotificationContextInterface|null $context
     */
    public function publish($routeName, array $routeParameters = [], NotificationInterface $notification, NotificationContextInterface $context = null);
}
