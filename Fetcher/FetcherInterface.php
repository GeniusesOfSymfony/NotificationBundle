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
    public function fetch($routeName, array $routeParameters = [], $start, $end);

    /**
     * @param array $routes
     * @param int      $start
     * @param int      $end
     *
     * @return array
     */
    public function multipleFetch(array $routes, $start, $end);

    /**
     * @param string $routeName
     * @param string[] $routeParameters
     * @param array           $options
     *
     * @return int
     */
    public function count($routeName, array $routeParameters, array $options = []);

    /**
     * @param array $routes
     * @param array $options
     *
     * @return array
     */
    public function multipleCount(array $routes, array $options = []);

    /**
     * @param string $routeName
     * @param string[] $routeParameters
     * @param string $uuid
     *
     * @return NotificationInterface
     *
     * @throws NotFoundNotificationException
     */
    public function getNotification($routeName, array $routeParameters = [], $uuid);

    /**
     * @param string                       $routeName
     * @param string[]                     $routeParameters
     * @param string|NotificationInterface $uuidOrNotification
     * @param bool                         $force
     *
     * @throws NotFoundNotificationException
     *
     * @return bool
     */
    public function markAsViewed($routeName, array $routeParameters = [], $uuidOrNotification, $force = false);
}
