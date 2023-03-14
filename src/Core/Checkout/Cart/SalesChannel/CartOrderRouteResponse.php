<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\SalesChannel;

use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('checkout')]
class CartOrderRouteResponse extends StoreApiResponse
{
    /**
     * @var OrderEntity
     */
    protected $object;

    public function __construct(OrderEntity $object)
    {
        parent::__construct($object);
    }

    public function getOrder(): OrderEntity
    {
        return $this->object;
    }
}
