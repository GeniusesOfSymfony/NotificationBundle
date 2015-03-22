<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Exception\NotFoundNotificationException;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface FetcherInterface
{
    /**
     * @param string|string[] $channels
     * @param int             $start
     * @param int             $end
     *
     * @return NotificationInterface[]|array
     */
    public function fetch($channels, $start, $end);

    /**
     * @param string|string[] $channels
     * @param array           $options
     *
     * @return int
     */
    public function count($channels, array $options = array());

    /**
     * @param string $channel
     * @param string $uuid
     *
     * @return NotificationInterface
     *
     * @throws NotFoundNotificationException
     */
    public function getNotification($channel, $uuid);

    /**
     * @param string                       $channel
     * @param string|NotificationInterface $uuidOrNotification
     * @param bool                         $force
     *
     * @throws NotFoundNotificationException
     *
     * @return bool
     */
    public function markAsViewed($channel, $uuidOrNotification, $force = false);
}
