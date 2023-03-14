<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductFeatureSetTranslationEntity>
 */
#[Package('inventory')]
class ProductFeatureSetTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductFeatureSetTranslationEntity::class;
    }
}
