<?php

namespace Gos\Bundle\NotificationBundle\Model;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
interface NotificationInterface extends \JsonSerializable
{
    /**
     * @param $key
     * @param $value
     */
    public function addExtra($key, $value);

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     */
    public function setLink($link);

    /**
     * @return array
     */
    public function getExtra();

    /**
     * @param array $extra
     */
    public function setExtra($extra);

    /**
     * @return int
     */
    public function getTimeout();

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout);

    /**
     * @return string
     */
    public function getUuid();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     */
    public function setIcon($icon);

    /**
     * @return \DateTime
     */
    public function getViewedAt();

    /**
     * @param \DateTime $viewedAt
     */
    public function setViewedAt($viewedAt = null);

    /**
     * @return \DateTime|string
     */
    public function getCreatedAt();

    /**
     * @param \DateTime|string $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     */
    public function setContent($content);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function getChannel();

    /**
     * @param string $url
     */
    public function setChannel($url);
}
