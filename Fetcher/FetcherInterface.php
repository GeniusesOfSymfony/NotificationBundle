<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Exception\NotFoundNotificationException;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface FetcherInterface
{
    /**
     * @param string $channel
     * @param int    $start
     * @param int    $end
     *
     * @return NotificationInterface[]|array
     */
    public function fetch($channel, $start, $end);

    /**
     * @param string[] $channels
     * @param int      $start
     * @param int      $end
     *
     * @return array
     */
    public function multipleFetch(array $channels, $start, $end);

    /**
     * @param string $channel
     * @param array  $options
     *
     * @return int
     */
    public function count($channel, array $options = []);

    /**
     * @param array $channels
     * @param array $options
     *
     * @return array
     */
    public function multipleCount(array $channels, array $options = []);

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
