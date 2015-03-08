<?php

namespace Gos\Bundle\NotificationBundle\Serializer;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;

interface NotificationContextSerializerInterface
{
    /**
     * @param NotificationContextInterface $context
     *
     * @return string
     */
    public function serialize(NotificationContextInterface $context);

    /**
     * @param string $message
     *
     * @return NotificationContextInterface
     */
    public function deserialize($message);
}
