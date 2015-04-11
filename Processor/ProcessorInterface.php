<?php

namespace Gos\Bundle\NotificationBundle\Processor;

use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;

interface ProcessorInterface
{
    /**
     * @param bool                  $wildcard
     * @param string                $pusherName
     * @param NotificationInterface $notification
     * @param PubSubRequest         $request
     *
     * @return string|string[]
     */
    public function process($wildcard, $pusherName, NotificationInterface $notification, PubSubRequest $request);
}
