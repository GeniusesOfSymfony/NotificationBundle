<?php

namespace Gos\Bundle\NotificationBundle\Procedure;

use Gos\Bundle\NotificationBundle\NotificationCenter;
use Gos\Bundle\WebSocketBundle\Client\ClientStorage;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Gos\Bundle\WebSocketBundle\RPC\RpcResponse;
use Ratchet\ConnectionInterface;

class NotificationProcedure implements RpcInterface
{
    /**
     * @var NotificationCenter
     */
    protected $notificationCenter;

    /**
     * @var ClientStorage
     */
    protected $clientStorage;

    /**
     * @param NotificationCenter $notificationCenter
     * @param ClientStorage      $clientStorage
     */
    public function __construct(NotificationCenter $notificationCenter, ClientStorage $clientStorage)
    {
        $this->notificationCenter = $notificationCenter;
        $this->clientStorage = $clientStorage;
    }

    /**
     * @param ConnectionInterface $conn
     * @param                     $params
     *
     * @return RpcResponse
     */
    public function fetch(ConnectionInterface $conn, WampRequest $request, $params)
    {
        $channel = $params['channel'];
        $start = $params['start'];
        $end = $params['end'];

        return new RpcResponse($this->notificationCenter->fetch($channel, $start, $end));
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function count(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $channels = $params['channels'];
        $options = $params['options'];

        return new RpcResponse($this->notificationCenter->count($channels, $options));
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function getNotification(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $channel = $params['channel'];
        $uuid = $params['uuid'];

        return new RpcResponse($this->notificationCenter->getNotification($channel, $uuid));
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function markAsViewed(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $channel = $params['channel'];
        $uuid = $params['uuid'];
        $force = (bool) $params['force'];

        return new RpcResponse($this->notificationCenter->markAsViewed($channel, $uuid, $force));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gos.notification.rpc';
    }
}
