<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Cart;

use Laser\Core\Content\Product\ProductCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('inventory')]
interface ProductGatewayInterface
{
    public function get(array $ids, SalesChannelContext $context): ProductCollection;
}
