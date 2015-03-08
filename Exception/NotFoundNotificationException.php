<?php

namespace Gos\Bundle\NotificationBundle\Exception;

class NotFoundNotificationException extends NotificationException
{
    public function __construct($uuid)
    {
        parent::__construct(sprintf('Notification %s not found', $uuid), 404);
    }
}
