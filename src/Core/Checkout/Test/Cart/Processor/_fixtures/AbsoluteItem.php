<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor\_fixtures;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Price\Struct\CurrencyPriceDefinition;
use Laser\Core\Defaults;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('checkout')]
class AbsoluteItem extends LineItem
{
    public function __construct(
        float $price,
        ?string $id = null
    ) {
        parent::__construct($id ?? Uuid::randomHex(), LineItem::DISCOUNT_LINE_ITEM);

        $this->priceDefinition = new CurrencyPriceDefinition(new PriceCollection([
            new Price(Defaults::CURRENCY, $price, $price, false),
        ]));
        $this->removable = true;
    }
}
