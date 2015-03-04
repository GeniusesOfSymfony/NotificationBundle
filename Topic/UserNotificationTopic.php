<?php

namespace Gos\Bundle\NotificationBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class UserNotificationTopic implements TopicInterface
{
    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic)
    {
        //nothing
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic)
    {
        //nothing
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic               $topic
     * @param string              $event
     * @param array               $exclude
     * @param array               $eligible
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, $event, array $exclude, array $eligible)
    {
        $topic->broadcast(json_decode($event, true), $exclude, $eligible);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return 'notification';
    }
}
