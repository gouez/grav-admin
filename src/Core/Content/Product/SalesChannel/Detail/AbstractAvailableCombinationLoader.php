<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\SalesChannel\Detail;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
abstract class AbstractAvailableCombinationLoader
{
    abstract public function getDecorated(): AbstractAvailableCombinationLoader;

    abstract public function load(string $productId, Context $context, string $salesChannelId): AvailableCombinationResult;
}
