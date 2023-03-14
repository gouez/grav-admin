<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor\_fixtures;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('checkout')]
class QuantityItem extends LineItem
{
    public function __construct(
        float $price,
        TaxRuleCollection $taxes,
        bool $good = true,
        int $quantity = 1
    ) {
        parent::__construct(Uuid::randomHex(), LineItem::PRODUCT_LINE_ITEM_TYPE, null, $quantity);

        $this->priceDefinition = new QuantityPriceDefinition($price, $taxes, $quantity);
        $this->setGood($good);
    }
}
