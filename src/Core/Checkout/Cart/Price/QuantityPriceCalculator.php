<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Price;

use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class QuantityPriceCalculator
{
    /**
     * @internal
     */
    public function __construct(
        private readonly GrossPriceCalculator $grossPriceCalculator,
        private readonly NetPriceCalculator $netPriceCalculator
    ) {
    }

    public function calculate(QuantityPriceDefinition $definition, SalesChannelContext $context): CalculatedPrice
    {
        if ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            $price = $this->grossPriceCalculator->calculate($definition, $context->getItemRounding());
        } else {
            $price = $this->netPriceCalculator->calculate($definition, $context->getItemRounding());
        }

        if ($context->getTaxState() === CartPrice::TAX_STATE_FREE) {
            $price->assign([
                'taxRules' => new TaxRuleCollection(),
                'calculatedTaxes' => new CalculatedTaxCollection(),
            ]);
        }

        return $price;
    }
}
