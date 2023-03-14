<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductCrossSellingAssignedProducts;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductCrossSellingAssignedProductsEntity>
 */
#[Package('inventory')]
class ProductCrossSellingAssignedProductsCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCrossSellingAssignedProductsEntity::class;
    }

    public function getApiAlias(): string
    {
        return 'product_cross_selling_assigned_products_collection';
    }

    /**
     * @return list<string>
     */
    public function getProductIds(): array
    {
        return $this->fmap(fn (ProductCrossSellingAssignedProductsEntity $entity) => $entity->getProductId());
    }

    public function sortByPosition(): void
    {
        $this->sort(fn (ProductCrossSellingAssignedProductsEntity $a, ProductCrossSellingAssignedProductsEntity $b) => $a->getPosition() <=> $b->getPosition());
    }
}
