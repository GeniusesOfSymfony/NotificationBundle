<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use Gos\Bundle\PubSubRouterBundle\Router\RouterInterface;
use Predis\Client;

/**
 * Class RedisPusher.
 */
class RedisPusher extends AbstractPusher
{
    const ALIAS = 'gos_redis';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param Client          $client
     * @param RouterInterface $router
     */
    public function __construct(Client $client, RouterInterface $router)
    {
        $this->client = $client;
        $this->router = $router;
    }

    /**
     * @param RouteInterface $route
     * @param array          $matrix
     *
     * @return array
     */
    protected function generateRoutes(RouteInterface $route, array $matrix)
    {
        $channels = [];
        foreach ($this->generateMatrixPermutations($matrix) as $parameters) {
            $channels[] = $this->router->generate((string) $route, $parameters);
        }

        return $channels;
    }

    /**
     * {@inheritdoc}
     */
    protected function doPush(
        MessageInterface $message,
        NotificationInterface $notification,
        PubSubRequest $request,
        Array $matrix,
        NotificationContextInterface $context = null
    ) {
        $pipe = $this->client->pipeline();

        foreach ($this->generateRoutes($request->getRoute(), $matrix) as $channel) {
            $pipe->lpush($channel, json_encode($notification->toArray()));
            $pipe->incr($channel . '-counter');
        }

        $pipe->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
