<?php

namespace Gos\Bundle\NotificationBundle\Model\Message;

interface MessageInterface
{
    /**
     * @return string
     */
    public function getKind();
    /**
     * @return string
     */
    public function getChannel();

    /**
     * @return string
     */
    public function getPayload();
}
