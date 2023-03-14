<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler\V630;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
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
class SyncTestPaymentHandler implements SynchronousPaymentHandlerInterface
{
    public function __construct(private readonly OrderTransactionStateHandler $transactionStateHandler)
    {
    }

    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        $transactionId = $transaction->getOrderTransaction()->getId();
        $order = $transaction->getOrder();

        $lineItems = $order->getLineItems();
        if ($lineItems === null) {
            throw new SyncPaymentProcessException($transactionId, 'lineItems is null');
        }

        $customer = $order->getOrderCustomer()->getCustomer();
        if ($customer === null) {
            throw new SyncPaymentProcessException($transactionId, 'customer is null');
        }

        $context = $salesChannelContext->getContext();
        $this->transactionStateHandler->process($transactionId, $context);
    }
}
