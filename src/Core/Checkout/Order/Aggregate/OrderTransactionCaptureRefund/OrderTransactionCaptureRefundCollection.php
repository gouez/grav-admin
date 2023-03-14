<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Aggregate\OrderTransactionCaptureRefund;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<OrderTransactionCaptureRefundEntity>
 */
#[Package('customer-order')]
class OrderTransactionCaptureRefundCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'order_transaction_capture_refund_collection';
    }

    protected function getExpectedClass(): string
    {
        return OrderTransactionCaptureRefundEntity::class;
    }
}
