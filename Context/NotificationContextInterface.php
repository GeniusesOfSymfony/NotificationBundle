<?php

namespace Gos\Bundle\NotificationBundle\Context;

interface NotificationContextInterface extends \JsonSerializable
{
    /**
     * @param array $pushers
     */
    public function setPushers(Array $pushers);

    /**
     * @param $pusher
     */
    public function addPusher($pusher);

    /**
     * @return array|\string[]
     */
    public function getPushers();
}
