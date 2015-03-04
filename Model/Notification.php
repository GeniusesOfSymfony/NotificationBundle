<?php

namespace Gos\Bundle\NotificationBundle\Model;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class Notification implements NotificationInterface
{
    const TYPE_INFO = 'info';
    const TYPE_DANGER = 'danger';
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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->uuid = $this->generateUuid();
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
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param \DateTime $viewedAt
     */
    public function setViewedAt($viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
