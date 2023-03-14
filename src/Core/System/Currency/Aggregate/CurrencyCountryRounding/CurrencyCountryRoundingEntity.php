<?php declare(strict_types=1);

namespace Laser\Core\System\Currency\Aggregate\CurrencyCountryRounding;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\CountryEntity;
use Laser\Core\System\Currency\CurrencyEntity;

#[Package('inventory')]
class CurrencyCountryRoundingEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $currencyId;

    /**
     * @var string
     */
    protected $countryId;

    /**
     * @var CashRoundingConfig
     */
    protected $itemRounding;

    /**
     * @var CashRoundingConfig
     */
    protected $totalRounding;

    /**
     * @var CurrencyEntity|null
     */
    protected $currency;

    /**
     * @var CountryEntity|null
     */
    protected $country;

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    public function getCountryId(): string
    {
        return $this->countryId;
    }

    public function setCountryId(string $countryId): void
    {
        $this->countryId = $countryId;
    }

    public function getItemRounding(): CashRoundingConfig
    {
        return $this->itemRounding;
    }

    public function setItemRounding(CashRoundingConfig $itemRounding): void
    {
        $this->itemRounding = $itemRounding;
    }

    public function getTotalRounding(): CashRoundingConfig
    {
        return $this->totalRounding;
    }

    public function setTotalRounding(CashRoundingConfig $totalRounding): void
    {
        $this->totalRounding = $totalRounding;
    }

    public function getCurrency(): ?CurrencyEntity
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyEntity $currency): void
    {
        $this->currency = $currency;
    }

    public function getCountry(): ?CountryEntity
    {
        return $this->country;
    }

    public function setCountry(CountryEntity $country): void
    {
        $this->country = $country;
    }
}
