<?php

namespace Gos\Bundle\NotificationBundle\Fetcher;

use Gos\Bundle\NotificationBundle\Model\NotificationInterface;

class RedisFetcher implements FetcherInterface
{
    /**
     * @param string $channel
     * @param int    $start
     * @param int    $end
     *
     * @return NotificationInterface[]|array
     */
    public function fetch($channel, $start, $end)
    {
        return array();
    }
}
