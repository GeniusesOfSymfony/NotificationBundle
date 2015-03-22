<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Exception\NotFoundNotificationException;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Redis\IndexOfElement;
use Gos\Bundle\NotificationBundle\Serializer\NotificationSerializerInterface;
use Predis\Client;
use Psr\Log\LoggerInterface;

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
    public function __construct(Client $client, NotificationSerializerInterface $serializer, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;

        //Command to enable to retrieve notification by uuid
        $client->getProfile()->defineCommand('lidxof', new IndexOfElement());
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($channels, $start, $end)
    {
        $channels = $this->adaptChannel($channels);
        $notifications = array();

        if (is_string($channels)) {
            $messages = $this->client->lrange($channels, $start, $end);

            foreach ($messages as $key => $message) {
                $notifications[] = $this->serializer->deserialize($message);
            }

            return $notifications;
        }

        if (is_array($channels)) {
            foreach ($channels as $channel) {
                $messages = $this->client->lrange($channel, $start, $end);

                foreach ($messages as $message) {
                    $notifications[$channel][] = $this->serializer->deserialize($message);
                }
            }

            return $notifications;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count($channels, array $options = array())
    {
        $channels = $this->adaptChannel($channels);

        $counter = array();
        $total = 0;

        $channels = (array) $channels;

        foreach ($channels as $channel) {
            $count = $this->client->get($channel . '-counter');
            $counter[$channel] = (int) $count;
            $total += $count;
        }

        $counter['total'] = $total;

        return $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification($channel, $uuid)
    {
        $channel = $this->adaptChannel($channel);

        $index = $this->client->lidxof($channel, 'uuid', $uuid);

        if ($index === -1) {
            throw new NotFoundNotificationException($uuid);
        }

        $message = $this->client->lindex($channel, $index);

        $notification = $this->serializer->deserialize($message);

        return $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsViewed($channel, $uuidOrNotification, $force = false)
    {
        $channel = $this->adaptChannel($channel);

        if ($uuidOrNotification instanceof NotificationInterface) {
            $uuid = $uuidOrNotification->getUuid();
        } else {
            $uuid = $uuidOrNotification;
        }

        $notification = $this->getNotification($channel, $uuid);

        if (true === $force) {
            $notification->setViewedAt(new \DateTime());
        } else {
            if (null === $notification->getViewedAt()) {
                $notification->setViewedAt(new \DateTime());
            }
        }

        $index = $this->client->lidxof($channel, 'uuid', $uuid);

        $this->client->lset($channel, $index, $this->serializer->serialize($notification));

        return true;
    }

    protected function adaptChannel($channel)
    {
        if (is_array($channel)) {
            return array_map([$this, 'adaptChannel'], $channel);
        }

        return str_replace('/', ':', $channel);
    }
}
