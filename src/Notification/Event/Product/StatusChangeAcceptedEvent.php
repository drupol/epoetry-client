<?php

namespace OpenEuropa\EPoetry\Notification\Event\Product;

/**
 * Event fired when the status of the product changes to "accepted".
 */
class StatusChangeAcceptedEvent extends BaseEvent
{
    public const NAME = 'epoetry.notification.product_status_change_accepted';
}
