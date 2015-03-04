<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

interface FetcherInterface
{
    /**
     * @param string $channel
     * @param int    $start
     * @param int    $end
     *
     * @return NotificationInterface[]|array
     */
    public function fetch($channel, $start, $end);
}
