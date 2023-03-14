<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class CustomerResponse extends StoreApiResponse
{
    /**
     * @var CustomerEntity
     */
    protected $object;

    public function __construct(CustomerEntity $object)
    {
        parent::__construct($object);
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->object;
    }
}
