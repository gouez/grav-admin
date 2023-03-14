<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart\PaymentHandler;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
interface RefundPaymentHandlerInterface extends PaymentHandlerInterface
{
    public function refund(string $refundId, Context $context): void;
}
