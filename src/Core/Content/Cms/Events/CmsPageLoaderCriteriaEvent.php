<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\Events;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('content')]
class CmsPageLoaderCriteriaEvent extends NestedEvent implements LaserSalesChannelEvent
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Criteria
     */
    protected $criteria;

    /**
     * @var SalesChannelContext
     */
    protected $salesChannelContext;

    public function __construct(
        Request $request,
        Criteria $criteria,
        SalesChannelContext $salesChannelContext
    ) {
        $this->request = $request;
        $this->criteria = $criteria;
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
