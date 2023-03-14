<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Shipping\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class ShippingMethodRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
