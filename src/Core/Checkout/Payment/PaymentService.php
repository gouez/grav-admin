<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment;

use Psr\Log\LoggerInterface;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Laser\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PaymentHandlerRegistry;
use Laser\Core\Checkout\Payment\Cart\PaymentTransactionChainProcessor;
use Laser\Core\Checkout\Payment\Cart\Token\TokenFactoryInterfaceV2;
use Laser\Core\Checkout\Payment\Cart\Token\TokenStruct;
use Laser\Core\Checkout\Payment\Exception\AsyncPaymentFinalizeException;
use Laser\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Laser\Core\Checkout\Payment\Exception\CustomerCanceledAsyncPaymentException;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Checkout\Payment\Exception\InvalidTransactionException;
use Laser\Core\Checkout\Payment\Exception\PaymentProcessException;
use Laser\Core\Checkout\Payment\Exception\SyncPaymentProcessException;
use Laser\Core\Checkout\Payment\Exception\TokenExpiredException;
use Laser\Core\Checkout\Payment\Exception\UnknownPaymentMethodException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

#[Package('checkout')]
class PaymentService
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PaymentTransactionChainProcessor $paymentProcessor,
        private readonly TokenFactoryInterfaceV2 $tokenFactory,
        private readonly PaymentHandlerRegistry $paymentHandlerRegistry,
        private readonly EntityRepository $orderTransactionRepository,
        private readonly OrderTransactionStateHandler $transactionStateHandler,
        private readonly LoggerInterface $logger,
        private readonly EntityRepository $orderRepository,
        private readonly SalesChannelContextServiceInterface $contextService
    ) {
    }

    /**
     * @throws AsyncPaymentProcessException
     * @throws InvalidOrderException
     * @throws SyncPaymentProcessException
     * @throws UnknownPaymentMethodException
     */
    public function handlePaymentByOrder(
        string $orderId,
        RequestDataBag $dataBag,
        SalesChannelContext $context,
        ?string $finishUrl = null,
        ?string $errorUrl = null
    ): ?RedirectResponse {
        if (!Uuid::isValid($orderId)) {
            throw new InvalidOrderException($orderId);
        }

        $criteria = new Criteria([$orderId]);
        $criteria->setTitle('payment-service::load-order');
        $order = $this->orderRepository
            ->search($criteria, $context->getContext())
            ->first();

        if ($order === null) {
            throw new InvalidOrderException($orderId);
        }

        if ($context->getCurrency()->getId() !== $order->getCurrencyId()) {
            $context = $this->contextService->get(
                new SalesChannelContextServiceParameters(
                    $context->getSalesChannelId(),
                    $context->getToken(),
                    $context->getContext()->getLanguageId(),
                    $order->getCurrencyId()
                )
            );
        }

        try {
            return $this->paymentProcessor->process($orderId, $dataBag, $context, $finishUrl, $errorUrl);
        } catch (PaymentProcessException $e) {
            $transactionId = $e->getOrderTransactionId();
            $this->logger->error('An error occurred during processing the payment', ['orderTransactionId' => $transactionId, 'exceptionMessage' => $e->getMessage()]);
            $this->transactionStateHandler->fail($transactionId, $context->getContext());
            if ($errorUrl !== null) {
                $errorUrl .= (parse_url($errorUrl, \PHP_URL_QUERY) ? '&' : '?') . 'error-code=' . $e->getErrorCode();

                return new RedirectResponse($errorUrl);
            }

            throw $e;
        }
    }

    /**
     * @throws AsyncPaymentFinalizeException
     * @throws InvalidTransactionException
     * @throws TokenExpiredException
     * @throws UnknownPaymentMethodException
     */
    public function finalizeTransaction(string $paymentToken, Request $request, SalesChannelContext $context): TokenStruct
    {
        $token = $this->tokenFactory->parseToken($paymentToken);

        if ($token->isExpired()) {
            $token->setException(new TokenExpiredException($paymentToken));
            $this->tokenFactory->invalidateToken($token->getToken());

            return $token;
        }

        $transactionId = $token->getTransactionId();

        if ($transactionId === null || !Uuid::isValid($transactionId)) {
            throw new AsyncPaymentProcessException((string) $transactionId, 'Payment JWT didn\'t contain a valid orderTransactionId');
        }

        $transaction = $this->getPaymentTransactionStruct($transactionId, $context->getContext());

        $paymentHandler = $this->getPaymentHandlerById($token->getPaymentMethodId(), $context->getContext());

        try {
            $paymentHandler->finalize($transaction, $request, $context);
        } catch (CustomerCanceledAsyncPaymentException $e) {
            $this->transactionStateHandler->cancel($transactionId, $context->getContext());
            $token->setException($e);
        } catch (PaymentProcessException $e) {
            $this->logger->error('An error occurred during finalizing async payment', ['orderTransactionId' => $transactionId, 'exceptionMessage' => $e->getMessage()]);
            $this->transactionStateHandler->fail($transactionId, $context->getContext());
            $token->setException($e);
        } finally {
            $this->tokenFactory->invalidateToken($token->getToken());
        }

        return $token;
    }

    /**
     * @throws UnknownPaymentMethodException
     */
    private function getPaymentHandlerById(string $paymentMethodId, Context $context): AsynchronousPaymentHandlerInterface
    {
        $handler = $this->paymentHandlerRegistry->getAsyncPaymentHandler($paymentMethodId);

        if (!$handler) {
            throw new UnknownPaymentMethodException($paymentMethodId);
        }

        return $handler;
    }

    /**
     * @throws InvalidTransactionException
     */
    private function getPaymentTransactionStruct(string $orderTransactionId, Context $context): AsyncPaymentTransactionStruct
    {
        $criteria = new Criteria([$orderTransactionId]);
        $criteria->setTitle('payment-service::load-transaction');
        $criteria->addAssociation('order');
        $criteria->addAssociation('paymentMethod.appPaymentMethod.app');
        /** @var OrderTransactionEntity|null $orderTransaction */
        $orderTransaction = $this->orderTransactionRepository->search($criteria, $context)->first();

        if ($orderTransaction === null) {
            throw new InvalidTransactionException($orderTransactionId);
        }

        return new AsyncPaymentTransactionStruct($orderTransaction, $orderTransaction->getOrder(), '');
    }
}
