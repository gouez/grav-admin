<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel;

use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class SalesChannelProductCollection extends ProductCollection
{
    public function getExpectedClass(): string
    {
        return SalesChannelProductEntity::class;
    }
}
