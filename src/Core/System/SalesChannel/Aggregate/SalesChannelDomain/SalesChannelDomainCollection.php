<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Aggregate\SalesChannelDomain;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<SalesChannelDomainEntity>
 */
#[Package('sales-channel')]
class SalesChannelDomainCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'sales_channel_domain_collection';
    }

    protected function getExpectedClass(): string
    {
        return SalesChannelDomainEntity::class;
    }
}
