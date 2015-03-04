<?php

namespace Gos\Bundle\NotificationBundle;

use Gos\Bundle\NotificationBundle\Fetcher\FetcherInterface;
use Gos\Bundle\NotificationBundle\Publisher\PublisherInterface;

interface NotificationManipulatorInterface extends PublisherInterface, FetcherInterface
{
}
