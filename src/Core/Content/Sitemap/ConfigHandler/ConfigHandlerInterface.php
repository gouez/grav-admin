<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\ConfigHandler;

use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface ConfigHandlerInterface
{
    public function getSitemapConfig(): array;
}
