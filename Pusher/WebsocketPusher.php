<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Bundle\PubSubRouterBundle\Router\RouteInterface;
use Gos\Bundle\PubSubRouterBundle\Router\RouterInterface;
use Gos\Component\WebSocketClient\Wamp\Client;
use Gos\Component\Yolo\Callback\PingBack;
use Gos\Component\Yolo\YoloInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class WebsocketPusher extends AbstractPusher implements YoloInterface
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

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @var bool
     */
    protected $connected;

    /**
     * @var Client
     */
    protected $ws;

    /**
     * @param string          $serverHost
     * @param int             $serverPort
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct($serverHost, $serverPort, RouterInterface $router, LoggerInterface $logger = null)
    {
        $this->serverHost = $serverHost;
        $this->serverPort = $serverPort;
        $this->router = $router;
        $this->logger = null === $logger ? new NullLogger() : $logger;
        $this->connected = false;
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
        if(false === $this->connected){
            $this->ws = new Client($this->serverHost, $this->serverPort);
            $this->ws->connect();
        }

        foreach ($this->generateRoutes($request->getRoute(), $matrix) as $channel) {
            $notification->setChannel($channel);
            $this->ws->publish($channel, json_encode($notification));
        }
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        $pingger = new PingBack($this->serverHost, $this->serverPort);

        return $pingger->ping();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
