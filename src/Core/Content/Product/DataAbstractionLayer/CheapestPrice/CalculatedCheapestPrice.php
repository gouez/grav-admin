<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\DataAbstractionLayer\CheapestPrice;

use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class CalculatedCheapestPrice extends CalculatedPrice
{
    /**
     * @var bool
     */
    protected $hasRange = false;

    public function hasRange(): bool
    {
        return $this->hasRange;
    }

    public function setHasRange(bool $hasRange): void
    {
        $this->hasRange = $hasRange;
    }

    public function getApiAlias(): string
    {
        return 'calculated_cheapest_price';
    }
}
