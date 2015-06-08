<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Exception\NotFoundNotificationException;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Redis\IndexOfElement;
use Gos\Bundle\NotificationBundle\Serializer\NotificationSerializerInterface;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RedisFetcher implements FetcherInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var NotificationSerializerInterface
     */
    protected $serializer;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @param Client                          $client
     * @param NotificationSerializerInterface $serializer
     * @param LoggerInterface                 $logger
     */
    public function __construct(
        Client $client,
        NotificationSerializerInterface $serializer,
        LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = null === $logger ? new NullLogger() : $logger;

        //Command to enable to retrieve notification by uuid
        $client->getProfile()->defineCommand('lidxof', new IndexOfElement());
    }

    /**
     * {@inheritdoc}
     */
    public function multipleFetch(array $channels, $start, $end)
    {
        $notifications = [];

        foreach ($channels as $url) {
            $messages = $this->client->lrange($url, $start, $end);

            foreach ($messages as $message) {
                $notifications[$url][] = $this->serializer->deserialize($message);
            }
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($channel, $start, $end)
    {
        $messages = $this->client->lrange($channel, $start, $end);
        $notifications = [];

        foreach ($messages as $key => $message) {
            $notifications[] = $this->serializer->deserialize($message);
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function multipleCount(array $channels, array $options = [])
    {
        $counter = array();
        $total = 0;

        foreach ($channels as $url) {
            $count = $this->client->get($url . '-counter');
            $counter[$url] = (int) $count;
            $total += $count;
        }

        $counter['total'] = $total;

        return $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function count($channel, array $options = [])
    {
        return $this->client->get($channel . '-counter');
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification($channel, $uuid)
    {
        return $this->doGetNotification($channel, $uuid);
    }

    /**
     * @param string $url
     * @param string $uuid
     *
     * @return NotificationInterface
     *
     * @throws NotFoundNotificationException
     */
    protected function doGetNotification($url, $uuid)
    {
        $index = $this->client->lidxof($url, 'uuid', $uuid);

        if ($index === -1) {
            throw new NotFoundNotificationException($uuid);
        }

        $message = $this->client->lindex($url, $index);

        $notification = $this->serializer->deserialize($message);

        return $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsViewed($channel, $uuidOrNotification, $force = false)
    {
        if ($uuidOrNotification instanceof NotificationInterface) {
            $uuid = $uuidOrNotification->getUuid();
        } else {
            $uuid = $uuidOrNotification;
        }

        $notification = $this->doGetNotification($channel, $uuid);

        if (true === $force) {
            $notification->setViewedAt(new \DateTime());
        } else {
            if (null === $notification->getViewedAt()) {
                $notification->setViewedAt(new \DateTime());
            }
        }

        $index = $this->client->lidxof($channel, 'uuid', $uuid);

        $this->client->pipeline(function ($pipe) use ($channel, $index, $notification) {
            $pipe->lset($channel, $index, $this->serializer->serialize($notification));
            $pipe->decr($channel . '-counter');
        });

        return true;
    }
}
