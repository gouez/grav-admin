<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount\Filter\Sorter;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemQuantity;
use Laser\Core\Checkout\Cart\LineItem\Group\LineItemQuantityCollection;
use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountPackageCollection;
use Laser\Core\Checkout\Promotion\Cart\Discount\Filter\FilterSorterInterface;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class FilterSorterPriceDesc implements FilterSorterInterface
{
    public function getKey(): string
    {
        return 'PRICE_DESC';
    }

    public function sort(DiscountPackageCollection $packages): DiscountPackageCollection
    {
        foreach ($packages as $package) {
            /** @var array $metaItems */
            $metaItems = $package->getMetaData()->getElements();

            usort($metaItems, static function (LineItemQuantity $a, LineItemQuantity $b) use ($package) {
                // we only have meta data here
                // so lets get the prices
                $priceA = $package->getCartItem($a->getLineItemId())->getPrice();
                $priceB = $package->getCartItem($b->getLineItemId())->getPrice();

                if ($priceA === null) {
                    return 1;
                }

                if ($priceB === null) {
                    return 0;
                }

                return $priceB->getUnitPrice() <=> $priceA->getUnitPrice();
            });

            $package->setMetaItems(new LineItemQuantityCollection($metaItems));
        }

        return $packages;
    }
}
