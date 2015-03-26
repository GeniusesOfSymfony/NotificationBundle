<?php

namespace Gos\Bundle\NotificationBundle\Loader;

interface LoaderInterface
{
    /**
     * @param array $options
     *
     * @return string[]|int[]
     */
    public function load(Array $options = array());

    /**
     * @param mixed $type
     *
     * @return bool
     */
    public function supports($type);
}