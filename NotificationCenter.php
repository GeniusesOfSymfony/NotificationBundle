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
    public function fetch($channel, $start, $end)
    {
        return $this->fetcher->fetch($channel, $start, $end);
    }

    /**
     * {@inheritdoc}
     */
    public function publish($channel, NotificationInterface $notification, NotificationContextInterface $context = null)
    {
        return $this->publisher->publish($channel, $notification, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function count($channel, array $options = [])
    {
        return $this->fetcher->count($channel, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification($channel, $uuid)
    {
        return $this->fetcher->getNotification($channel, $uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsViewed($channel, $uuidOrNotification, $force = false)
    {
        return $this->fetcher->markAsViewed($channel, $uuidOrNotification, $force);
    }

    /**
     * {@inheritdoc}
     */
    public function multipleFetch(array $channels, $start, $end)
    {
        return $this->fetcher->multipleFetch($channels, $start, $end);
    }

    /**
     * {@inheritdoc}
     */
    public function multipleCount(array $channels, array $options = [])
    {
        return $this->fetcher->multipleCount($channels, $options);
    }
}
