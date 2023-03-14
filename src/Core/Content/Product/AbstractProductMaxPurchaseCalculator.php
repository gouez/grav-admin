<?php declare(strict_types=1);

namespace Laser\Core\Content\Product;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('inventory')]
abstract class AbstractProductMaxPurchaseCalculator
{
    abstract public function getDecorated(): AbstractProductMaxPurchaseCalculator;

    abstract public function calculate(Entity $product, SalesChannelContext $context): int;
}
