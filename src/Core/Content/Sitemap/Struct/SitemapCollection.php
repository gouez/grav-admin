<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Collection;

/**
 * @extends Collection<Sitemap>
 */
#[Package('sales-channel')]
class SitemapCollection extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return Sitemap::class;
    }
}
