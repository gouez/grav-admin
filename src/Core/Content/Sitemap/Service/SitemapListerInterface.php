<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Service;

use Laser\Core\Content\Sitemap\Struct\Sitemap;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
interface SitemapListerInterface
{
    /**
     * @return Sitemap[]
     */
    public function getSitemaps(SalesChannelContext $salesChannelContext): array;
}
