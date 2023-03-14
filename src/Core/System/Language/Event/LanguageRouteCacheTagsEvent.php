<?php declare(strict_types=1);

namespace Laser\Core\System\Language\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class LanguageRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
