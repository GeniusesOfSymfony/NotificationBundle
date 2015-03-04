<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

/**
 * Class PusherRegistry.
 */
class PusherRegistry
{
    /**
     * @var PusherInterface[]
     */
    protected $pushers;

    public function __construct()
    {
        $this->pushers = array();
    }

    /**
     * @param PusherInterface $pusher
     */
    public function addPusher(PusherInterface $pusher)
    {
        $this->pushers[$pusher->getAlias()] = $pusher;
    }

    /**
     * @param array $specificPushers
     *
     * @return PusherInterface[]
     */
    public function getPushers(Array $specificPushers = null)
    {
        if (null === $specificPushers || empty($specificPushers)) {
            return $this->pushers;
        }

        $pushers = array();

        foreach ($this->pushers as $pusher) {
            if (in_array($pusher->getAlias(), $specificPushers)) {
                $pushers[] = $pusher;
            }
        }

        return $pushers;
    }
}
