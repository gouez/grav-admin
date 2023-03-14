<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel;

use Laser\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductAvailableFilter extends MultiFilter
{
    public function __construct(
        string $salesChannelId,
        int $visibility = ProductVisibilityDefinition::VISIBILITY_ALL
    ) {
        parent::__construct(
            self::CONNECTION_AND,
            [
                new RangeFilter('product.visibilities.visibility', [RangeFilter::GTE => $visibility]),
                new EqualsFilter('product.visibilities.salesChannelId', $salesChannelId),
                new EqualsFilter('product.active', true),
            ]
        );
    }
}
