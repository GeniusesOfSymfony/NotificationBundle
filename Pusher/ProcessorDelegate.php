<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Processor\ProcessorInterface;

interface ProcessorDelegate
{
    /**
     * @param string             $attribute
     * @param ProcessorInterface $processor
     */
    public function addProcessor($attribute, ProcessorInterface $processor);
}
