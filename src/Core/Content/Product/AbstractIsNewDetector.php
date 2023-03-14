<?php declare(strict_types=1);

namespace Laser\Core\Content\Product;

use Laser\Core\Framework\DataAbstractionLayer\Entity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('inventory')]
abstract class AbstractIsNewDetector
{
    abstract public function getDecorated(): AbstractIsNewDetector;

    abstract public function isNew(Entity $product, SalesChannelContext $context): bool;
}
