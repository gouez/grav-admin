<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Aggregate\SalesChannelTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<SalesChannelTranslationEntity>
 */
#[Package('sales-channel')]
class SalesChannelTranslationCollection extends EntityCollection
{
    /**
     * @return list<string>
     */
    public function getSalesChannelIds(): array
    {
        return $this->fmap(fn (SalesChannelTranslationEntity $salesChannelTranslation) => $salesChannelTranslation->getSalesChannelId());
    }

    public function filterBySalesChannelId(string $id): self
    {
        return $this->filter(fn (SalesChannelTranslationEntity $salesChannelTranslation) => $salesChannelTranslation->getSalesChannelId() === $id);
    }

    /**
     * @return list<string>
     */
    public function getLanguageIds(): array
    {
        return $this->fmap(fn (SalesChannelTranslationEntity $salesChannelTranslation) => $salesChannelTranslation->getLanguageId());
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(fn (SalesChannelTranslationEntity $salesChannelTranslation) => $salesChannelTranslation->getLanguageId() === $id);
    }

    public function getApiAlias(): string
    {
        return 'sales_channel_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return SalesChannelTranslationEntity::class;
    }
}
