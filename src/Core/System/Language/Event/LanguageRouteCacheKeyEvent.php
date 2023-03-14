<?php declare(strict_types=1);

namespace Laser\Core\System\Language\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheKeyEvent;
use Laser\Core\Framework\Log\Package;

#[Package('system-settings')]
class LanguageRouteCacheKeyEvent extends StoreApiRouteCacheKeyEvent
{
}
