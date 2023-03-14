<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('core')]
class SalesChannelContextRestoredEvent extends NestedEvent
{
    public function __construct(
        private readonly SalesChannelContext $restoredContext,
        private readonly SalesChannelContext $currentContext
    ) {
    }

    public function getRestoredSalesChannelContext(): SalesChannelContext
    {
        return $this->restoredContext;
    }

    public function getContext(): Context
    {
        return $this->restoredContext->getContext();
    }

    public function getCurrentSalesChannelContext(): SalesChannelContext
    {
        return $this->currentContext;
    }
}
