<?php declare(strict_types=1);

namespace Laser\Core\Content\Category\Event;

use Laser\Core\Content\Category\Tree\Tree;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('content')]
class NavigationLoadedEvent extends NestedEvent implements LaserSalesChannelEvent
{
    /**
     * @var Tree
     */
    protected $navigation;

    /**
     * @var SalesChannelContext
     */
    protected $salesChannelContext;

    public function __construct(
        Tree $navigation,
        SalesChannelContext $salesChannelContext
    ) {
        $this->navigation = $navigation;
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getNavigation(): Tree
    {
        return $this->navigation;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
