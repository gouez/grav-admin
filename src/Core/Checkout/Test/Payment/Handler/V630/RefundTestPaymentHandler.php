<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler\V630;

use Laser\Core\Checkout\Order\Aggregate\OrderTransactionCaptureRefund\OrderTransactionCaptureRefundStateHandler;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\RefundPaymentHandlerInterface;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('checkout')]
class RefundTestPaymentHandler implements RefundPaymentHandlerInterface
{
    public function __construct(private readonly OrderTransactionCaptureRefundStateHandler $stateHandler)
    {
    }

    public function refund(string $refundId, Context $context): void
    {
        $this->stateHandler->complete($refundId, $context);
    }
}
