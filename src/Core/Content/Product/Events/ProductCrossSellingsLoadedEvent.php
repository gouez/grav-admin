<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElementCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('inventory')]
class ProductCrossSellingsLoadedEvent extends Event implements LaserSalesChannelEvent
{
    public function __construct(
        private readonly CrossSellingElementCollection $result,
        private readonly SalesChannelContext $salesChannelContext
    ) {
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getCrossSellings(): CrossSellingElementCollection
    {
        return $this->result;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
