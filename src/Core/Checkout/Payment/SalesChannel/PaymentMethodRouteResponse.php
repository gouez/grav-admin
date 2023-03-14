<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\SalesChannel;

use Laser\Core\Checkout\Payment\PaymentMethodCollection;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('checkout')]
class PaymentMethodRouteResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult
     */
    protected $object;

    public function __construct(EntitySearchResult $paymentMethods)
    {
        parent::__construct($paymentMethods);
    }

    public function getPaymentMethods(): PaymentMethodCollection
    {
        /** @var PaymentMethodCollection $collection */
        $collection = $this->object->getEntities();

        return $collection;
    }
}
