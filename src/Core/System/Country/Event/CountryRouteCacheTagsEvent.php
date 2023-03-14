<?php declare(strict_types=1);

namespace Laser\Core\System\Country\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class CountryRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
