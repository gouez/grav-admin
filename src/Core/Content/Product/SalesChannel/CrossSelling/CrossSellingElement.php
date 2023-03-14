<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\CrossSelling;

use Laser\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('inventory')]
class CrossSellingElement extends Struct
{
    protected ProductCrossSellingEntity $crossSelling;

    protected ProductCollection $products;

    protected int $total;

    protected ?string $streamId = null;

    public function getCrossSelling(): ProductCrossSellingEntity
    {
        return $this->crossSelling;
    }

    public function setCrossSelling(ProductCrossSellingEntity $crossSelling): void
    {
        $this->crossSelling = $crossSelling;
    }

    public function getProducts(): ProductCollection
    {
        return $this->products;
    }

    public function setProducts(ProductCollection $products): void
    {
        $this->products = $products;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getApiAlias(): string
    {
        return 'cross_selling_element';
    }

    public function getStreamId(): ?string
    {
        return $this->streamId;
    }

    public function setStreamId(?string $streamId): void
    {
        $this->streamId = $streamId;
    }
}
