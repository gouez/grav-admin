<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;
use Laser\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateEntity;

#[Package('customer-order')]
class CancelOrderRouteResponse extends StoreApiResponse
{
    /**
     * @var StateMachineStateEntity
     */
    protected $object;

    public function __construct(StateMachineStateEntity $object)
    {
        parent::__construct($object);
    }

    public function getState(): StateMachineStateEntity
    {
        return $this->object;
    }
}
