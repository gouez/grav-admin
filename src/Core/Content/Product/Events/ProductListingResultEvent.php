<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Events;

use Laser\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('inventory')]
class ProductListingResultEvent extends NestedEvent implements LaserSalesChannelEvent
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
     * @var ProductListingResult
     */
    protected $result;

    public function __construct(
        Request $request,
        ProductListingResult $result,
        SalesChannelContext $context
    ) {
        $this->request = $request;
        $this->context = $context;
        $this->result = $result;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function getResult(): ProductListingResult
    {
        return $this->result;
    }
}
