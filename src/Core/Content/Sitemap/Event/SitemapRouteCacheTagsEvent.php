<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Event;

use Laser\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
class SitemapRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
