<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\Events;

use Laser\Core\Content\Cms\CmsPageEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('content')]
class CmsPageLoadedEvent extends NestedEvent implements LaserSalesChannelEvent
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityCollection<CmsPageEntity>
     */
    protected $result;

    /**
     * @var SalesChannelContext
     */
    protected $salesChannelContext;

    /**
     * @param EntityCollection<CmsPageEntity> $result
     */
    public function __construct(
        Request $request,
        EntityCollection $result,
        SalesChannelContext $salesChannelContext
    ) {
        $this->request = $request;
        $this->result = $result;
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return EntityCollection<CmsPageEntity>
     */
    public function getResult(): EntityCollection
    {
        return $this->result;
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
