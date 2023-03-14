<?php declare(strict_types=1);

namespace Laser\Core\System\Country\Aggregate\CountryTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<CountryTranslationEntity>
 */
#[Package('system-settings')]
class CountryTranslationCollection extends EntityCollection
{
    public function getCountryIds(): array
    {
        return $this->fmap(fn (CountryTranslationEntity $countryTranslation) => $countryTranslation->getCountryId());
    }

    public function filterByCountryId(string $id): self
    {
        return $this->filter(fn (CountryTranslationEntity $countryTranslation) => $countryTranslation->getCountryId() === $id);
    }

    public function getLanguageIds(): array
    {
        return $this->fmap(fn (CountryTranslationEntity $countryTranslation) => $countryTranslation->getLanguageId());
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(fn (CountryTranslationEntity $countryTranslation) => $countryTranslation->getLanguageId() === $id);
    }

    public function getApiAlias(): string
    {
        return 'country_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return CountryTranslationEntity::class;
    }
}
