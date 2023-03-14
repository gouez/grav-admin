<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment\Handler\V630;

use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Laser\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Exception\CustomerCanceledAsyncPaymentException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Package('checkout')]
class AsyncTestPaymentHandler implements AsynchronousPaymentHandlerInterface
{
    final public const REDIRECT_URL = 'https://laser.com';

    public function __construct(private readonly OrderTransactionStateHandler $transactionStateHandler)
    {
    }

    public function pay(AsyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $context = $salesChannelContext->getContext();

        $this->transactionStateHandler->process($transaction->getOrderTransaction()->getId(), $context);

        return new RedirectResponse(self::REDIRECT_URL);
    }

    public function finalize(
        AsyncPaymentTransactionStruct $transaction,
        Request $request,
        SalesChannelContext $salesChannelContext
    ): void {
        $context = $salesChannelContext->getContext();

        if ($request->query->getBoolean('cancel')) {
            throw new CustomerCanceledAsyncPaymentException(
                $transaction->getOrderTransaction()->getId(),
                'Async Test Payment canceled'
            );
        }

        $this->transactionStateHandler->paid($transaction->getOrderTransaction()->getId(), $context);
    }
}
