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
     * @param string $channel
     *
     * @return string
     */
    protected function transliterateChannel($channel)
    {
        return str_replace('/', ':', $channel);
    }

    /**
     * @param ConnectionInterface $conn
     * @param                     $params
     *
     * @return RpcResponse
     */
    public function fetch(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $start = $params['start'];
        $end = $params['end'];
        $channel = $this->transliterateChannel($params['channel']);

        if (is_array($channel)) {
            $result = $this->notificationCenter->multipleFetch($channel, $start, $end);
        } else {
            $result = $this->notificationCenter->fetch($channel, $start, $end);
        }

        return new RpcResponse($result);
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function count(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $options = $params['options'];
        $channel = $this->transliterateChannel($params['channel']);

        if (is_array($channel)) {
            $result = $this->notificationCenter->multipleCount($channel, $options);
        } else {
            $result = $this->notificationCenter->count($channel, $options);
        }

        return new RpcResponse($result);
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function getNotification(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $uuid = $params['uuid'];
        $channel = $this->transliterateChannel($params['channel']);

        return new RpcResponse($channel, $this->notificationCenter->getNotification($channel, $uuid));
    }

    /**
     * @param ConnectionInterface $conn
     * @param array               $params
     *
     * @return RpcResponse
     */
    public function markAsViewed(ConnectionInterface $conn, WampRequest $request, Array $params)
    {
        $channel = $this->transliterateChannel($params['channel']);
        $uuid = $params['uuid'];

        if (isset($params['force'])) {
            $force = (bool) $params['force'];
            $result = $this->notificationCenter->markAsViewed($channel, $uuid, $force);
        } else {
            $result = $this->notificationCenter->markAsViewed($channel, $uuid);
        }

        return new RpcResponse($result);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gos.notification.rpc';
    }
}
