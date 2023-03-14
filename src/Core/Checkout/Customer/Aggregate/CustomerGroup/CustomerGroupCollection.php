<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Aggregate\CustomerGroup;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<CustomerGroupEntity>
 */
#[Package('customer-order')]
class CustomerGroupCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'customer_group_collection';
    }

    protected function getExpectedClass(): string
    {
        return CustomerGroupEntity::class;
    }
}
