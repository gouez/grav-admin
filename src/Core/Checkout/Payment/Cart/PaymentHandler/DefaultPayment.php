<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart\PaymentHandler;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Laser\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class DefaultPayment implements SynchronousPaymentHandlerInterface
{
    /**
     * @var OrderTransactionStateHandler
     */
    protected $transactionStateHandler;

    /**
     * @internal
     */
    public function __construct(OrderTransactionStateHandler $transactionStateHandler)
    {
        $this->transactionStateHandler = $transactionStateHandler;
    }

    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        // needed for payment methods like Cash on delivery and Paid in advance
    }
}
