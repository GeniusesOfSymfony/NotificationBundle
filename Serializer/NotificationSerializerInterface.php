<?php

namespace Gos\Bundle\NotificationBundle\Serializer;

use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface NotificationSerializerInterface
{
    /**
     * @param NotificationInterface $notification
     *
     * @return string
     */
    public function serialize(NotificationInterface $notification);

    /**
     * @param string $message
     *
     * @return NotificationInterface
     */
    public function deserialize($message);
}
