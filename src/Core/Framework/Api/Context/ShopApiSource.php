<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Context;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class ShopApiSource extends SalesChannelApiSource
{
    public string $type = 'shop-api';
}
