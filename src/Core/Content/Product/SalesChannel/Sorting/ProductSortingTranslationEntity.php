<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Sorting;

use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductSortingTranslationEntity extends TranslationEntity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $productSortingId;

    /**
     * @var ProductSortingEntity|null
     */
    protected $productSorting;

    /**
     * @var string|null
     */
    protected $label;

    public function getProductSortingId(): string
    {
        return $this->productSortingId;
    }

    public function setProductSortingId(string $productSortingId): void
    {
        $this->productSortingId = $productSortingId;
    }

    public function getProductSorting(): ?ProductSortingEntity
    {
        return $this->productSorting;
    }

    public function setProductSorting(?ProductSortingEntity $productSorting): void
    {
        $this->productSorting = $productSorting;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getApiAlias(): string
    {
        return 'product_sorting_translation';
    }
}
