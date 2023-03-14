<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler;

use Laser\Core\Checkout\Payment\Cart\PaymentHandler\SynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class SyncTestFailedPaymentHandler implements SynchronousPaymentHandlerInterface
{
    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        throw new SyncPaymentProcessException($transaction->getOrderTransaction()->getId(), 'This is a TestPaymentHandler which will always fail');
    }
}
