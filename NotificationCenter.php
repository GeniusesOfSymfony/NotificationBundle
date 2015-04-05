<?php

namespace Gos\Bundle\NotificationBundle;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Fetcher\FetcherInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Publisher\PublisherInterface;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class NotificationCenter implements NotificationManipulatorInterface
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var FetcherInterface
     */
    protected $fetcher;

    /**
     * @param PublisherInterface $publisher
     * @param FetcherInterface   $fetcher
     */
    public function __construct(
        PublisherInterface $publisher,
        FetcherInterface $fetcher
    ) {
        $this->publisher = $publisher;
        $this->fetcher = $fetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($routeName, array $routeParameters = [], $start, $end)
    {
        return $this->fetcher->fetch($routeName, $routeParameters, $start, $end);
    }

    /**
     * {@inheritdoc}
     */
    public function publish($routeName, array $routeParameters = [], NotificationInterface $notification, NotificationContextInterface $context = null)
    {
        return $this->publisher->publish($routeName, $routeParameters, $notification, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function count($routeName, array $routeParameters = [], array $options = [])
    {
        return $this->fetcher->count($routeName, $routeParameters, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification($routeName, array $routeParameters = [], $uuid)
    {
        return $this->fetcher->getNotification($routeName, $routeParameters, $uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsViewed($routeName, array $routeParameters = [], $uuidOrNotification, $force = false)
    {
        return $this->fetcher->markAsViewed($routeName, $routeParameters, $uuidOrNotification, $force);
    }

    /**
     * {@inheritdoc}
     */
    public function multipleFetch(array $routes, $start, $end)
    {
        return $this->fetcher->multipleFetch($routes, $start, $end);
    }

    /**
     * {@inheritdoc}
     */
    public function multipleCount(array $routes, array $options = [])
    {
        return $this->fetcher->multipleCount($routes, $options);
    }
}
