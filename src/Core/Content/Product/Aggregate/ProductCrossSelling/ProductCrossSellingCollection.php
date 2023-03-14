<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductCrossSelling;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductCrossSellingEntity>
 */
#[Package('inventory')]
class ProductCrossSellingCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCrossSellingEntity::class;
    }

    public function getApiAlias(): string
    {
        return 'product_cross_selling_collection';
    }
}
