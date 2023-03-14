<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Laser\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Laser\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Laser\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Exception\InvalidPriceFieldTypeException;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PriceDefinitionFactory
{
    public function factory(Context $context, array $priceDefinition, string $lineItemType): PriceDefinitionInterface
    {
        if (!isset($priceDefinition['type'])) {
            throw new InvalidPriceFieldTypeException('none');
        }

        return match ($priceDefinition['type']) {
            QuantityPriceDefinition::TYPE => QuantityPriceDefinition::fromArray($priceDefinition),
            AbsolutePriceDefinition::TYPE => new AbsolutePriceDefinition((float) $priceDefinition['price']),
            PercentagePriceDefinition::TYPE => new PercentagePriceDefinition($priceDefinition['percentage']),
            default => throw new InvalidPriceFieldTypeException($priceDefinition['type']),
        };
    }
}
