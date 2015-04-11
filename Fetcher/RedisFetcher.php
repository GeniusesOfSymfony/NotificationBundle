<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Exception\NotFoundNotificationException;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\NotificationBundle\Redis\IndexOfElement;
use Gos\Bundle\NotificationBundle\Serializer\NotificationSerializerInterface;
use Gos\Bundle\PubSubRouterBundle\Generator\GeneratorInterface;
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
     * @var GeneratorInterface
     */
    protected $routeGenerator;

    /**
     * @param GeneratorInterface              $routeGenerator
     * @param Client                          $client
     * @param NotificationSerializerInterface $serializer
     * @param LoggerInterface                 $logger
     */
    public function __construct(
        GeneratorInterface $routeGenerator,
        Client $client,
        NotificationSerializerInterface $serializer,
        LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->routeGenerator = $routeGenerator;

        //Command to enable to retrieve notification by uuid
        $client->getProfile()->defineCommand('lidxof', new IndexOfElement());
    }

    /**
     * @param array $routes
     *
     * @return array
     */
    protected function getChannels(array $routes)
    {
        $channels = [];

        foreach ($routes as $routeName => $routeParameters) {
            $channels[] = $this->routeGenerator->generate($routeName, $routeParameters);
        }

        return $channels;
    }

    /**
     * {@inheritdoc}
     */
    public function multipleFetch(array $routes, $start, $end)
    {
        $channels = $this->getChannels($routes);
        $notifications = [];

        foreach ($channels as $channel) {
            $messages = $this->client->lrange($channel, $start, $end);

            foreach ($messages as $message) {
                $notifications[$channel][] = $this->serializer->deserialize($message);
            }
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($routeName, array $routeParameters = [], $start, $end)
    {
        $channel = $this->routeGenerator->generate($routeName, $routeParameters);
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
    public function multipleCount(array $routes, array $options = [])
    {
        $channels = $this->getChannels($routes);

        $counter = array();
        $total = 0;

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
    public function count($routeName, array $routeParameters = [], array $options = [])
    {
        $channel = $this->routeGenerator->generate($routeName, $routeParameters);

        return $this->client->get($channel . '-counter');
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification($routeName, array $routeParameters = [], $uuid)
    {
        return $this->doGetNotification($this->routeGenerator->generate($routeName, $routeParameters), $uuid);
    }

    /**
     * @param string $channel
     * @param string $uuid
     *
     * @return NotificationInterface
     *
     * @throws NotFoundNotificationException
     */
    protected function doGetNotification($channel, $uuid)
    {
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
    public function markAsViewed($routeName, array $routeParameters = [], $uuidOrNotification, $force = false)
    {
        $channel = $this->routeGenerator->generate($routeName, $routeParameters);

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
