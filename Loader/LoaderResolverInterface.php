<?php

namespace Gos\Bundle\NotificationBundle\Loader;

interface LoaderResolverInterface
{
    /**
     * @param mixed $type
     *
     * @return bool|LoaderInterface
     */
    public function resolve($type);
}