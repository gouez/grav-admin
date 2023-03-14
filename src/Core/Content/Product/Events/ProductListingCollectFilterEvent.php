<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Content\Product\SalesChannel\Listing\FilterCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('inventory')]
class ProductListingCollectFilterEvent extends NestedEvent implements LaserSalesChannelEvent
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var FilterCollection
     */
    protected $filters;

    public function __construct(
        Request $request,
        FilterCollection $filters,
        SalesChannelContext $context
    ) {
        $this->request = $request;
        $this->context = $context;
        $this->filters = $filters;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getFilters(): FilterCollection
    {
        return $this->filters;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }
}
