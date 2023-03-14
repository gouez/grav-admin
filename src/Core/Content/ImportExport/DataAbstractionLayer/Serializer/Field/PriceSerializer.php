<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Field;

use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\PriceField;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\Currency\CurrencyEntity;

#[Package('core')]
class PriceSerializer extends FieldSerializer
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $currencyRepository)
    {
    }

    public function serialize(Config $config, Field $entity, $prices): iterable
    {
        if (!$prices) {
            return;
        }

        $isoPrices = [];
        foreach ($prices as $price) {
            $price = $price instanceof Struct ? $price->jsonSerialize() : $price;
            $currencyId = $price['currencyId'];
            $currency = $this->mapToCurrencyIso($currencyId);

            if (isset($price['listPrice']) && $price['listPrice'] instanceof Struct) {
                $price['listPrice'] = $price['listPrice']->jsonSerialize();
            }

            $isoPrices[$currency] = $price;
            if ($currencyId === Defaults::CURRENCY) {
                $isoPrices['DEFAULT'] = $isoPrices[$currency];
            }
        }

        yield $entity->getPropertyName() => $isoPrices;
    }

    public function deserialize(Config $config, Field $field, $record): ?array
    {
        $prices = [];

        if (!\is_array($record)) {
            return null;
        }

        foreach ($record as $currencyIso => $price) {
            $currency = $this->getCurrencyIdFromIso($currencyIso);

            if ($currency === null || !$this->isValidPrice($price)) {
                continue;
            }

            $listPrice = null;
            if (isset($price['listPrice']) && $this->isValidPrice($price['listPrice'])) {
                $listPrice = new Price($currency, (float) $price['listPrice']['net'], (float) $price['listPrice']['gross'], (bool) ($price['listPrice']['linked'] ?? false));
            }

            $priceStruct = new Price($currency, (float) $price['net'], (float) $price['gross'], (bool) ($price['linked'] ?? false), $listPrice);
            $prices[$currency] = $priceStruct->jsonSerialize();

            if (isset($prices[$currency]['listPrice']) && $prices[$currency]['listPrice'] instanceof Price) {
                $prices[$currency]['listPrice'] = $prices[$currency]['listPrice']->jsonSerialize();
            }
        }

        if (empty($prices)) {
            return null;
        }

        return $prices;
    }

    public function supports(Field $field): bool
    {
        return $field instanceof PriceField
            || ($field instanceof JsonField && $field->getPropertyName() === 'price');
    }

    public function setRegistry(SerializerRegistry $serializerRegistry): void
    {
    }

    public function isValidPrice(array $price): bool
    {
        return filter_var($price['net'] ?? null, \FILTER_VALIDATE_FLOAT) !== false
            && filter_var($price['gross'] ?? null, \FILTER_VALIDATE_FLOAT) !== false;
    }

    private function mapToCurrencyIso(string $currencyId): string
    {
        $currency = $this->currencyRepository
            ->search(new Criteria([$currencyId]), Context::createDefaultContext())
            ->first();

        return $currency ? $currency->getIsoCode() : $currencyId;
    }

    private function getCurrencyIdFromIso(string $iso): ?string
    {
        if ($iso === 'DEFAULT') {
            return Defaults::CURRENCY;
        }

        if (Uuid::isValid($iso)) {
            $criteria = new Criteria([$iso]);
        } else {
            $criteria = (new Criteria())->addFilter(new EqualsFilter('isoCode', $iso));
        }

        /** @var CurrencyEntity|null $currency */
        $currency = $this->currencyRepository->search($criteria, Context::createDefaultContext())->first();

        return $currency === null ? null : $currency->getId();
    }
}
