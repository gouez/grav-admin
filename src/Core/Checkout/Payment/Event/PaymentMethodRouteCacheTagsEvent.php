<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PaymentMethodRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
