<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel\Struct;

use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('content')]
class ProductSliderStruct extends Struct
{
    /**
     * @var ProductCollection|null
     */
    protected $products = null;

    protected ?string $streamId = null;

    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }

    public function setProducts(ProductCollection $products): void
    {
        $this->products = $products;
    }

    public function getApiAlias(): string
    {
        return 'cms_product_slider';
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
