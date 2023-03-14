<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface SalesChannelAware extends FlowEventAware
{
    public function getSalesChannelId(): string;
}
