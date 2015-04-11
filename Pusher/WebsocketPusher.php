<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use Gos\Bundle\PubSubRouterBundle\Router\RouterInterface;
use Gos\Component\WebSocketClient\Wamp\Client;

class WebsocketPusher extends AbstractPusher
{
    const ALIAS = 'gos_websocket';

    /**
     * @var string
     */
    protected $serverHost;

    /**
     * @var string
     */
    protected $serverPort;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param string          $serverHost
     * @param string          $serverPort
     * @param RouterInterface $router
     */
    public function __construct($serverHost, $serverPort, RouterInterface $router)
    {
        $this->serverHost = $serverHost;
        $this->serverPort = $serverPort;
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
     * @param MessageInterface             $message
     * @param NotificationInterface        $notification
     * @param PubSubRequest                $request
     * @param array                        $matrix
     * @param NotificationContextInterface $context
     *
     * @throws \Gos\Component\WebSocketClient\Exception\BadResponseException
     */
    protected function doPush(
        MessageInterface $message,
        NotificationInterface $notification,
        PubSubRequest $request,
        array $matrix,
        NotificationContextInterface $context = null
    ) {
        $socket = new Client($this->serverHost, $this->serverPort);
        $socket->connect('/');

        foreach ($this->generateRoutes($request->getRoute(), $matrix) as $channel) {
            $socket->publish($channel, json_encode($notification));
        }

        $socket->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
