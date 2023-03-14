<?php declare(strict_types=1);

namespace Laser\Core\System\Salutation\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheKeyEvent;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class SalutationRouteCacheKeyEvent extends StoreApiRouteCacheKeyEvent
{
}
