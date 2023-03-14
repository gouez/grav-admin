<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Cart\PaymentHandler;

use Laser\Core\Checkout\Payment\Cart\SyncPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
interface SynchronousPaymentHandlerInterface extends PaymentHandlerInterface
{
    /**
     * The pay function will be called after the customer completed the order.
     * Allows to process the order and store additional information.
     *
     * Throw a @see SyncPaymentProcessException exception if an error ocurres while processing the payment
     *
     * @throws SyncPaymentProcessException
     */
    public function pay(SyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void;
}
