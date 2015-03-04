<?php

namespace Gos\Bundle\NotificationBundle\Model\Message;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $kind;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var string
     */
    protected $payload;

    /**
     * @param string $kind
     * @param string $channel
     * @param string $payload
     */
    public function __construct($kind, $channel, $payload)
    {
        $this->kind = $kind;
        $this->channel = $channel;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
