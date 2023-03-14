<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductFeatureSet;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductFeatureSetEntity>
 */
#[Package('inventory')]
class ProductFeatureSetCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductFeatureSetEntity::class;
    }
}
