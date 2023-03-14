<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount\Filter;

use Laser\Core\Checkout\Promotion\Cart\Discount\DiscountPackageCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
interface FilterPickerInterface
{
    public function getKey(): string;

    public function pickItems(DiscountPackageCollection $units): DiscountPackageCollection;
}
