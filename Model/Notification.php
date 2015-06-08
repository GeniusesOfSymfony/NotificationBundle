<?php

namespace Gos\Bundle\NotificationBundle\Model;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class Notification implements NotificationInterface
{
    const TYPE_INFO = 'info';
    const TYPE_ERROR = 'error';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';

    /** @var string */
    protected $uuid;

    /** @var string */
    protected $type;

    /** @var string */
    protected $icon;

    /** @var  \DateTime */
    protected $viewedAt;

    /** @var  \DateTime */
    protected $createdAt;

    /** @var string */
    protected $title;

    /** @var  string */
    protected $content;

    /** @var  string */
    protected $link;

    /** @var  array */
    protected $extra;

    /** @var  int */
    protected $timeout;

    /** @var  string */
    protected $channel;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->uuid = $this->generateUuid();
        $this->extra = [];
        $this->timeout = 5000;
    }

    /**
     * @return array
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtra($key, $value)
    {
        $this->extra[$key] = $value;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * UUID v4.
     *
     * @return string
     */
    protected function generateUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param \DateTime|string $viewedAt
     */
    public function setViewedAt($viewedAt = null)
    {
        if ($viewedAt instanceof \DateTime) {
            $this->viewedAt = $viewedAt;
        } else {
            $this->viewedAt = \DateTime::createFromFormat(\DateTime::W3C, $viewedAt);
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        if ($createdAt instanceof \DateTime) {
            $this->createdAt = $createdAt;
        } else {
            $this->createdAt = \DateTime::createFromFormat(\DateTime::W3C, $createdAt);
        }
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**** Transformer Methods *****/

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'uuid' => $this->uuid,
            'type' => $this->type,
            'icon' => $this->icon,
            'viewed_at' => $this->viewedAt !== null ? $this->viewedAt->format(\DateTime::W3C) : null,
            'created_at' => $this->createdAt->format(\DateTime::W3C),
            'content' => $this->content,
            'title' => $this->title,
            'link' => $this->link,
            'extra' => $this->extra,
            'timeout' => $this->timeout,
            'channel' => $this->channel,
        );
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
