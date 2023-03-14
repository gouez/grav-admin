<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class OrderRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    /**
     * @var array<string, bool>
     */
    protected $paymentChangeable = [];

    public function getObject(): Struct
    {
        return new ArrayStruct([
            'orders' => $this->object,
            'paymentChangeable' => $this->paymentChangeable,
        ], 'order-route-response-struct');
    }

    public function getOrders(): EntitySearchResult
    {
        return $this->object;
    }

    public function getPaymentsChangeable(): array
    {
        return $this->paymentChangeable;
    }

    public function setPaymentChangeable(array $paymentChangeable): void
    {
        $this->paymentChangeable = $paymentChangeable;
    }

    public function addPaymentChangeable(array $paymentChangeable): void
    {
        $this->paymentChangeable = array_merge($this->paymentChangeable, $paymentChangeable);
    }
}
