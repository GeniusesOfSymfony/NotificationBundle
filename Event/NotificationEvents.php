<?php

namespace Gos\Bundle\NotificationBundle\Event;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
final class NotificationEvents
{
    const NOTIFICATION_PUBLISHED = 'gos_notification.notification.published';
    const NOTIFICATION_PUSHED = 'gos_notification.notification.pushed';

    const NOTIFICATION_CONSUMED = 'gos_notification.notification.consumed';
}
