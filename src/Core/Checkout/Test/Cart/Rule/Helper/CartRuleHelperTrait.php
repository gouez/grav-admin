<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Rule\Helper;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Laser\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\ListPrice;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

#[Package('business-ops')]
trait CartRuleHelperTrait
{
    protected static function createLineItem(
        string $type = LineItem::PRODUCT_LINE_ITEM_TYPE,
        int $quantity = 1,
        ?string $referenceId = null
    ): LineItem {
        return new LineItem(Uuid::randomHex(), $type, $referenceId, $quantity);
    }

    protected static function createLineItemWithDeliveryInfo(
        bool $freeDelivery,
        int $quantity = 1,
        ?float $weight = 50.0,
        ?float $height = null,
        ?float $width = null,
        ?float $length = null,
        int $stock = 9999
    ): LineItem {
        return self::createLineItem(LineItem::PRODUCT_LINE_ITEM_TYPE, $quantity)->setDeliveryInformation(
            new DeliveryInformation(
                $stock,
                $weight,
                $freeDelivery,
                null,
                (new DeliveryTime())->assign([
                    'min' => 1,
                    'max' => 3,
                    'unit' => 'weeks',
                    'name' => '1-3 weeks',
                ]),
                $height,
                $width,
                $length
            )
        );
    }

    protected static function createContainerLineItem(LineItemCollection $childLineItemCollection): LineItem
    {
        return self::createLineItem('container-type')->setChildren($childLineItemCollection);
    }

    protected static function createLineItemWithPrice(string $type, float $price, ?ListPrice $listPrice = null): LineItem
    {
        return self::createLineItem($type)->setPrice(
            new CalculatedPrice(
                $price,
                $price,
                new CalculatedTaxCollection(),
                new TaxRuleCollection(),
                1,
                null,
                $listPrice
            )
        );
    }

    protected static function createCart(LineItemCollection $lineItemCollection): Cart
    {
        $cart = new Cart(Uuid::randomHex());
        $cart->addLineItems($lineItemCollection);

        return $cart;
    }
}
