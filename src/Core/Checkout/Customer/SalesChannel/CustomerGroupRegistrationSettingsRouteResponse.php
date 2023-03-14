<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class CustomerGroupRegistrationSettingsRouteResponse extends StoreApiResponse
{
    /**
     * @var CustomerGroupEntity
     */
    protected $object;

    public function __construct(CustomerGroupEntity $object)
    {
        parent::__construct($object);
    }

    public function getRegistration(): CustomerGroupEntity
    {
        return $this->object;
    }
}
