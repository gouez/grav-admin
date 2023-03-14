<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler\V630;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PreparedPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PreparedPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class MultipleTestPaymentHandler implements SynchronousPaymentHandlerInterface, PreparedPaymentHandlerInterface
{
    public function validate(
        Cart $cart,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context
    ): Struct {
        return new ArrayStruct();
    }

    public function capture(
        PreparedPaymentTransactionStruct $transaction,
        RequestDataBag $requestDataBag,
        SalesChannelContext $context,
        Struct $preOrderPaymentStruct
    ): void {
    }

    public function pay(
        SyncPaymentTransactionStruct $transaction,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): void {
    }
}
