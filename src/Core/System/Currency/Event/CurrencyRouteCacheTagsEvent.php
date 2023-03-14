<?php declare(strict_types=1);

namespace Laser\Core\System\Currency\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class CurrencyRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
