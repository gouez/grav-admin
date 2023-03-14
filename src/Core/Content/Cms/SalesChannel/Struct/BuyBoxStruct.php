<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel\Struct;

use Laser\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Laser\Core\Content\Property\PropertyGroupCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('content')]
class BuyBoxStruct extends Struct
{
    /**
     * @var string|null
     */
    protected $productId;

    /**
     * @var int
     */
    protected $totalReviews;

    /**
     * @var SalesChannelProductEntity|null
     */
    protected $product;

    /**
     * @var PropertyGroupCollection|null
     */
    protected $configuratorSettings;

    public function getProduct(): ?SalesChannelProductEntity
    {
        return $this->product;
    }

    public function getConfiguratorSettings(): ?PropertyGroupCollection
    {
        return $this->configuratorSettings;
    }

    public function setConfiguratorSettings(?PropertyGroupCollection $configuratorSettings): void
    {
        $this->configuratorSettings = $configuratorSettings;
    }

    public function setProduct(SalesChannelProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getTotalReviews(): ?int
    {
        return $this->totalReviews;
    }

    public function setTotalReviews(int $totalReviews): void
    {
        $this->totalReviews = $totalReviews;
    }

    public function getApiAlias(): string
    {
        return 'cms_buy_box';
    }
}
