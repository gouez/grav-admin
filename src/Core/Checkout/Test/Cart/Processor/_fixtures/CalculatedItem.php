<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor\_fixtures;

use Laser\Core\Checkout\Cart\Price\CashRounding;
use Laser\Core\Checkout\Cart\Price\GrossPriceCalculator;
use Laser\Core\Checkout\Cart\Price\NetPriceCalculator;
use Laser\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Laser\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Checkout\Cart\Tax\TaxCalculator;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class CalculatedItem extends QuantityItem
{
    public function __construct(
        float $price,
        TaxRuleCollection $taxes,
        SalesChannelContext $context,
        bool $good = true,
        int $quantity = 1
    ) {
        parent::__construct($price, $taxes, $good, $quantity);

        $calculator = new QuantityPriceCalculator(
            new GrossPriceCalculator(new TaxCalculator(), new CashRounding()),
            new NetPriceCalculator(new TaxCalculator(), new CashRounding())
        );

        \assert($this->getPriceDefinition() instanceof QuantityPriceDefinition);
        $this->price = $calculator->calculate($this->getPriceDefinition(), $context);
    }
}
