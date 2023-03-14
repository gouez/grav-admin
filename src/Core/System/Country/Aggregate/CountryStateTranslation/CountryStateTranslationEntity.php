<?php declare(strict_types=1);

namespace Laser\Core\System\Country\Aggregate\CountryStateTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\Aggregate\CountryState\CountryStateEntity;

#[Package('system-settings')]
class CountryStateTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    /**
     * @var string
     */
    protected $countryStateId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var CountryStateEntity|null
     */
    protected $countryState;

    public function getCountryStateId(): string
    {
        return $this->countryStateId;
    }

    public function setCountryStateId(string $countryStateId): void
    {
        $this->countryStateId = $countryStateId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCountryState(): ?CountryStateEntity
    {
        return $this->countryState;
    }

    public function setCountryState(CountryStateEntity $countryState): void
    {
        $this->countryState = $countryState;
    }
}
