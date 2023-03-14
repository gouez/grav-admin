<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Aggregate\OrderTransactionCapture;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<OrderTransactionCaptureEntity>
 */
#[Package('customer-order')]
class OrderTransactionCaptureCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'order_transaction_capture_collection';
    }

    protected function getExpectedClass(): string
    {
        return OrderTransactionCaptureEntity::class;
    }
}
