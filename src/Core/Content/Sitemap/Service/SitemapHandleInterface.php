<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Service;

use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
interface SitemapHandleInterface
{
    public function write(array $urls): void;

    public function finish(): void;
}
