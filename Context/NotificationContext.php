<?php

namespace Gos\Bundle\NotificationBundle\Context;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class NotificationContext implements NotificationContextInterface
{
    /**
     * @var PusherIdentity|null
     */
    protected $pusherIdentity;

    /**
     * @var string[]
     */
    protected $pushers;

    /**
     * @param PusherIdentity|null $pusherIdentity
     */
    public function __construct(PusherIdentity $pusherIdentity = null)
    {
        $this->pusherIdentity = $pusherIdentity;
        $this->pushers = array();
    }

    /**
     * @param array $pushers
     */
    public function setPushers(Array $pushers)
    {
        $this->pushers = $pushers;
    }

    /**
     * @param $pusher
     */
    public function addPusher($pusher)
    {
        $this->pushers[] = $pusher;
    }

    /**
     * @return array|\string[]
     */
    public function getPushers()
    {
        return $this->pushers;
    }

    /**
     * @return bool
     */
    public function hasPusherIdentity()
    {
        return null !== $this->pusherIdentity;
    }

    /**
     * @return PusherIdentity|null
     */
    public function getPusherIdentity()
    {
        return $this->pusherIdentity;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'pusher_identity' => $this->pusherIdentity,
            'pushers' => $this->pushers,
        );
    }
}
