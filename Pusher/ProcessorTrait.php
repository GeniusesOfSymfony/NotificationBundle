<?php

namespace Gos\Bundle\NotificationBundle\Pusher;

use Gos\Bundle\NotificationBundle\Processor\ProcessorInterface;

trait ProcessorTrait
{
    /**
     * @var ProcessorInterface[]
     */
    protected $processors = [];

    /**
     * @param string             $attribute
     * @param ProcessorInterface $processor
     */
    public function addProcessor($attribute, ProcessorInterface $processor)
    {
        $this->processors[$attribute] = $processor;
    }
}
