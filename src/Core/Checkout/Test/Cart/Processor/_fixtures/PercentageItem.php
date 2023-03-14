<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor\_fixtures;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('checkout')]
class PercentageItem extends LineItem
{
    public function __construct(
        int $percentage,
        ?string $id = null
    ) {
        parent::__construct($id ?? Uuid::randomHex(), LineItem::DISCOUNT_LINE_ITEM);

        $this->priceDefinition = new PercentagePriceDefinition($percentage);
        $this->removable = true;
    }
}
