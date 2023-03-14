<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount\Filter\Picker;

use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountPackageCollection;
use Laser\Core\Checkout\Promotion\Cart\Discount\Filter\FilterPickerInterface;
use Laser\Core\Framework\Log\Package;

/**
 * The vertical picker makes sure that the filter
 * iteration is taking place within each group.
 * So if you decide to get the first 2 cheapest items,
 * then it will return the first 2 cheapest items from each group.
 */
#[Package('checkout')]
class VerticalPicker implements FilterPickerInterface
{
    public function getKey(): string
    {
        return 'VERTICAL';
    }

    public function pickItems(DiscountPackageCollection $units): DiscountPackageCollection
    {
        return new DiscountPackageCollection($units);
    }
}
