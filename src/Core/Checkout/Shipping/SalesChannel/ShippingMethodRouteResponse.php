<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Shipping\SalesChannel;

use Laser\Core\Checkout\Shipping\ShippingMethodCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('checkout')]
class ShippingMethodRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $shippingMethods)
    {
        parent::__construct($shippingMethods);
    }

    public function getShippingMethods(): ShippingMethodCollection
    {
        /** @var ShippingMethodCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
