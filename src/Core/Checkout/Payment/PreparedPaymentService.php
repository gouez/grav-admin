<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment;

use Psr\Log\LoggerInterface;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PreparedPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PreparedPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Checkout\Payment\Exception\PaymentProcessException;
use Laser\Core\Checkout\Payment\Exception\UnknownPaymentMethodException;
use Laser\Core\Checkout\Payment\Exception\ValidatePreparedPaymentException;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\StateMachine\Loader\InitialStateIdLoader;

#[Package('checkout')]
class PreparedPaymentService
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly EntityRepository $appPaymentMethodRepository,
        private readonly LoggerInterface $logger,
        private readonly InitialStateIdLoader $initialStateIdLoader
    ) {
    }

    public function handlePreOrderPayment(
        Cart $cart,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext
    ): ?Struct {
        try {
            $paymentHandler = $this->getPaymentHandlerFromSalesChannelContext($salesChannelContext);
            if (!$paymentHandler) {
                throw new UnknownPaymentMethodException($salesChannelContext->getPaymentMethod()->getId());
            }

            if (!($paymentHandler instanceof PreparedPaymentHandlerInterface)) {
                return null;
            }

            return $paymentHandler->validate($cart, $dataBag, $salesChannelContext);
        } catch (PaymentProcessException|ValidatePreparedPaymentException $e) {
            $customer = $salesChannelContext->getCustomer();
            $customerId = $customer !== null ? $customer->getId() : '';
            $this->logger->error('An error occurred during processing the validation of the payment. The order has not been placed yet.', ['customerId' => $customerId, 'exceptionMessage' => $e->getMessage()]);

            throw $e;
        }
    }

    public function handlePostOrderPayment(
        OrderEntity $order,
        RequestDataBag $dataBag,
        SalesChannelContext $salesChannelContext,
        ?Struct $preOrderStruct
    ): void {
        try {
            $transaction = $this->getTransaction($order, $salesChannelContext);
            if ($transaction === null) {
                return;
            }

            $paymentHandler = $this->getPaymentHandlerFromTransaction($transaction);

            if (!($paymentHandler instanceof PreparedPaymentHandlerInterface)
                || $preOrderStruct === null) {
                return;
            }

            $preparedTransactionStruct = new PreparedPaymentTransactionStruct($transaction, $order);
            $paymentHandler->capture($preparedTransactionStruct, $dataBag, $salesChannelContext, $preOrderStruct);
        } catch (PaymentProcessException $e) {
            $this->logger->error('An error occurred during processing the capture of the payment. The order has been placed.', ['orderId' => $order->getId(), 'exceptionMessage' => $e->getMessage()]);

            throw $e;
        }
    }

    private function getTransaction(OrderEntity $order, SalesChannelContext $salesChannelContext): ?OrderTransactionEntity
    {
        $transactions = $order->getTransactions();
        if ($transactions === null) {
            throw new InvalidOrderException($order->getId());
        }

        $transactions = $transactions->filterByStateId(
            $this->initialStateIdLoader->get(OrderTransactionStates::STATE_MACHINE)
        );

        return $transactions->last();
    }

    private function getPaymentHandlerFromTransaction(OrderTransactionEntity $transaction): PaymentHandlerInterface
    {
        $paymentMethod = $transaction->getPaymentMethod();
        if ($paymentMethod === null) {
            throw new UnknownPaymentMethodException($transaction->getPaymentMethodId());
        }

        $paymentHandler = $this->paymentHandlerRegistry->getPaymentMethodHandler($paymentMethod->getId());
        if (!$paymentHandler) {
            throw new UnknownPaymentMethodException($paymentMethod->getId());
        }

        return $paymentHandler;
    }

    private function getPaymentHandlerFromSalesChannelContext(SalesChannelContext $salesChannelContext): ?PaymentHandlerInterface
    {
        $paymentMethod = $salesChannelContext->getPaymentMethod();

        if (($appPaymentMethod = $paymentMethod->getAppPaymentMethod()) && $appPaymentMethod->getApp()) {
            return $this->paymentHandlerRegistry->getPaymentMethodHandler($paymentMethod->getId());
        }

        $criteria = new Criteria();
        $criteria->setTitle('prepared-payment-handler');
        $criteria->addAssociation('app');
        $criteria->addFilter(new EqualsFilter('paymentMethodId', $paymentMethod->getId()));

        $appPaymentMethod = $this->appPaymentMethodRepository->search($criteria, $salesChannelContext->getContext())->first();
        $paymentMethod->setAppPaymentMethod($appPaymentMethod);

        return $this->paymentHandlerRegistry->getPaymentMethodHandler($paymentMethod->getId());
    }
}
