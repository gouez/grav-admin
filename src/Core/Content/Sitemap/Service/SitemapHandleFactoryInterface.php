<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Service;

use League\Flysystem\FilesystemOperator;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('sales-channel')]
interface SitemapHandleFactoryInterface
{
    public function create(FilesystemOperator $filesystem, SalesChannelContext $context, ?string $domain = null): SitemapHandleInterface;
}
