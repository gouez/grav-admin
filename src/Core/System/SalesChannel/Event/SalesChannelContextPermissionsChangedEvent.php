<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('core')]
class SalesChannelContextPermissionsChangedEvent extends NestedEvent implements LaserSalesChannelEvent
{
    /**
     * @var array
     */
    protected $permissions = [];

    public function __construct(
        private readonly SalesChannelContext $salesChannelContext,
        array $permissions
    ) {
        $this->permissions = $permissions;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
