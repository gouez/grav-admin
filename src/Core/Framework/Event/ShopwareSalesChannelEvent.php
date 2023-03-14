<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('core')]
interface LaserSalesChannelEvent extends LaserEvent
{
    public function getSalesChannelContext(): SalesChannelContext;
}
