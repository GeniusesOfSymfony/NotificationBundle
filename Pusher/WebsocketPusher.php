<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Context\NotificationContextInterface;
use Gos\Bundle\NotificationBundle\Model\Message\MessageInterface;
use Gos\Bundle\NotificationBundle\Model\NotificationInterface;
use Gos\Bundle\PubSubRouterBundle\Request\PubSubRequest;
use Gos\Component\WebSocketClient\Wamp\Client;

class WebsocketPusher implements PusherInterface
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
     * @param string $serverHost
     * @param string $serverPort
     */
    public function __construct($serverHost, $serverPort)
    {
        $this->serverHost = $serverHost;
        $this->serverPort = $serverPort;
    }

    /**
     * {@inheritdoc}
     */
    public function push(MessageInterface $message, NotificationInterface $notification, PubSubRequest $request, NotificationContextInterface $context = null)
    {
        $socket = new Client($this->serverHost, $this->serverPort);
        $sessionId = $socket->connect('/');
        $socket->publish(str_replace(':', '/', $message->getChannel()), json_encode($notification));
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
