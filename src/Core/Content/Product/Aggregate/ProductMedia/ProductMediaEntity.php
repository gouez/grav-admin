<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductMedia;

use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Content\Product\ProductEntity;
use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductMediaEntity extends Entity
{
    use EntityIdTrait;
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string
     */
    protected $mediaId;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var ProductCollection|null
     */
    protected $coverProducts;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getCoverProducts(): ?ProductCollection
    {
        return $this->coverProducts;
    }

    public function setCoverProducts(ProductCollection $coverProducts): void
    {
        $this->coverProducts = $coverProducts;
    }
}
