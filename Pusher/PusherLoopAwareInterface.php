<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use React\EventLoop\LoopInterface;

interface PusherLoopAwareInterface
{
    /**
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop);
}
