<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Service;

use Laser\Core\Content\Sitemap\Exception\AlreadyLockedException;
use Laser\Core\Content\Sitemap\Struct\SitemapGenerationResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
interface SitemapExporterInterface
{
    public const SITEMAP_URL_LIMIT = 49999;

    public const STRATEGY_MANUAL = 1;
    public const STRATEGY_SCHEDULED_TASK = 2;
    public const STRATEGY_LIVE = 3;

    /**
     * @throws AlreadyLockedException
     */
    public function generate(SalesChannelContext $context, bool $force = false, ?string $lastProvider = null, ?int $offset = null): SitemapGenerationResult;
}
