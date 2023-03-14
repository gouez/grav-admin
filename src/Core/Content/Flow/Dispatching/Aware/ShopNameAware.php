<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface ShopNameAware extends FlowEventAware
{
    public const SHOP_NAME = 'shopName';

    public function getShopName(): string;
}
