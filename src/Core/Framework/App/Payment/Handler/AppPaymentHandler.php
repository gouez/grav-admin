<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Payment\Handler;

use Psr\Http\Client\ClientExceptionInterface;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Laser\Core\Checkout\Order\Aggregate\OrderTransactionCaptureRefund\OrderTransactionCaptureRefundDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransactionCaptureRefund\OrderTransactionCaptureRefundEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\PreparedPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\RefundPaymentHandlerInterface;
use Laser\Core\Checkout\Payment\Cart\PreparedPaymentTransactionStruct;
use Laser\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Laser\Core\Checkout\Payment\Exception\CapturePreparedPaymentException;
use Laser\Core\Checkout\Payment\Exception\RefundException;
use Laser\Core\Checkout\Payment\Exception\ValidatePreparedPaymentException;
use Laser\Core\Framework\App\Aggregate\AppPaymentMethod\AppPaymentMethodEntity;
use Laser\Core\Framework\App\Payment\Payload\PaymentPayloadService;
use Laser\Core\Framework\App\Payment\Payload\Struct\CapturePayload;
use Laser\Core\Framework\App\Payment\Payload\Struct\RefundPayload;
use Laser\Core\Framework\App\Payment\Payload\Struct\ValidatePayload;
use Laser\Core\Framework\App\Payment\Response\CaptureResponse;
use Laser\Core\Framework\App\Payment\Response\RefundResponse;
use Laser\Core\Framework\App\Payment\Response\ValidateResponse;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\ArrayStruct;
use Laser\Core\Framework\Struct\Struct;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Laser\Core\System\StateMachine\StateMachineRegistry;
use Laser\Core\System\StateMachine\Transition;

/**
 * @internal only for use by the app-system
 */
#[Package('core')]
class AppPaymentHandler implements RefundPaymentHandlerInterface, PreparedPaymentHandlerInterface
{
    public function __construct(
        protected OrderTransactionStateHandler $transactionStateHandler,
        protected StateMachineRegistry $stateMachineRegistry,
        protected PaymentPayloadService $payloadService,
        protected EntityRepository $refundRepository
    ) {
    }

    public function validate(Cart $cart, RequestDataBag $requestDataBag, SalesChannelContext $context): Struct
    {
        $appPaymentMethod = $context->getPaymentMethod()->getAppPaymentMethod();
        if ($appPaymentMethod === null) {
            throw new ValidatePreparedPaymentException('Loaded data invalid');
        }

        $validateUrl = $appPaymentMethod->getValidateUrl();
        if (empty($validateUrl)) {
            return new ArrayStruct();
        }

        $payload = $this->buildValidatePayload($cart, $requestDataBag, $context);
        $app = $appPaymentMethod->getApp();
        if ($app === null) {
            throw new ValidatePreparedPaymentException('App not defined');
        }

        try {
            $response = $this->payloadService->request($validateUrl, $payload, $app, ValidateResponse::class, $context->getContext());
        } catch (ClientExceptionInterface $exception) {
            throw new ValidatePreparedPaymentException(sprintf('App error: %s', $exception->getMessage()));
        }

        if (!$response instanceof ValidateResponse) {
            throw new ValidatePreparedPaymentException('Invalid app response');
        }

        if ($response->getMessage()) {
            throw new ValidatePreparedPaymentException($response->getMessage());
        }

        return new ArrayStruct($response->getPreOrderPayment());
    }

    public function capture(PreparedPaymentTransactionStruct $transaction, RequestDataBag $requestDataBag, SalesChannelContext $context, Struct $preOrderPaymentStruct): void
    {
        $captureUrl = $this->getAppPaymentMethod($transaction->getOrderTransaction())->getCaptureUrl();
        if (empty($captureUrl)) {
            return;
        }

        $payload = $this->buildCapturePayload($transaction, $preOrderPaymentStruct);
        $app = $this->getAppPaymentMethod($transaction->getOrderTransaction())->getApp();
        if ($app === null) {
            throw new CapturePreparedPaymentException($transaction->getOrderTransaction()->getId(), 'App not defined');
        }

        try {
            $response = $this->payloadService->request($captureUrl, $payload, $app, CaptureResponse::class, $context->getContext());
        } catch (ClientExceptionInterface $exception) {
            throw new CapturePreparedPaymentException($transaction->getOrderTransaction()->getId(), sprintf('App error: %s', $exception->getMessage()));
        }

        if (!$response instanceof CaptureResponse) {
            throw new CapturePreparedPaymentException($transaction->getOrderTransaction()->getId(), 'Invalid app response');
        }

        if ($response->getMessage() || $response->getStatus() === StateMachineTransitionActions::ACTION_FAIL) {
            throw new CapturePreparedPaymentException($transaction->getOrderTransaction()->getId(), $response->getMessage() ?? 'Payment was reported as failed.');
        }

        if (empty($response->getStatus())) {
            return;
        }

        $this->stateMachineRegistry->transition(
            new Transition(
                OrderTransactionDefinition::ENTITY_NAME,
                $transaction->getOrderTransaction()->getId(),
                $response->getStatus(),
                'stateId'
            ),
            $context->getContext()
        );
    }

    public function refund(string $refundId, Context $context): void
    {
        $criteria = new Criteria([$refundId]);
        $criteria->addAssociation('stateMachineState');
        $criteria->addAssociation('transactionCapture.transaction.order');
        $criteria->addAssociation('transactionCapture.transaction.paymentMethod.appPaymentMethod.app');
        $criteria->addAssociation('transactionCapture.positions');

        $refund = $this->refundRepository->search($criteria, $context)->first();

        if (!$refund->getTransactionCapture()
            || !$refund->getTransactionCapture()->getTransaction()
            || !$refund->getTransactionCapture()->getTransaction()->getOrder()
        ) {
            return;
        }

        $transaction = $refund->getTransactionCapture()->getTransaction();
        $paymentMethod = $this->getAppPaymentMethod($transaction);
        $refundUrl = $paymentMethod->getRefundUrl();

        if (!$refundUrl) {
            return;
        }

        $app = $paymentMethod->getApp();

        if (!$app) {
            throw new RefundException($refund->getId(), 'App not defined');
        }

        $payload = $this->buildRefundPayload($refund, $refund->getTransactionCapture()->getTransaction()->getOrder());

        try {
            $response = $this->payloadService->request($refundUrl, $payload, $app, RefundResponse::class, $context);
        } catch (ClientExceptionInterface $exception) {
            throw new RefundException($refund->getId(), sprintf('App error: %s', $exception->getMessage()));
        }

        if (!$response instanceof RefundResponse) {
            throw new RefundException($refund->getId(), 'Invalid app response');
        }

        if ($response->getMessage() || $response->getStatus() === StateMachineTransitionActions::ACTION_FAIL) {
            throw new RefundException($refund->getId(), $response->getMessage() ?? 'Refund was reported as failed.');
        }

        if (empty($response->getStatus())) {
            return;
        }

        $this->stateMachineRegistry->transition(
            new Transition(
                OrderTransactionCaptureRefundDefinition::ENTITY_NAME,
                $refund->getId(),
                $response->getStatus(),
                'stateId'
            ),
            $context
        );
    }

    protected function getAppPaymentMethod(OrderTransactionEntity $orderTransaction): AppPaymentMethodEntity
    {
        $paymentMethod = $orderTransaction->getPaymentMethod();
        if ($paymentMethod === null) {
            throw new AsyncPaymentProcessException($orderTransaction->getId(), 'Loaded data invalid');
        }

        $appPaymentMethod = $paymentMethod->getAppPaymentMethod();
        if ($appPaymentMethod === null) {
            throw new AsyncPaymentProcessException($orderTransaction->getId(), 'Loaded data invalid');
        }

        return $appPaymentMethod;
    }

    protected function buildRefundPayload(OrderTransactionCaptureRefundEntity $refund, OrderEntity $order): RefundPayload
    {
        return new RefundPayload(
            $refund,
            $order
        );
    }

    protected function buildValidatePayload(Cart $cart, RequestDataBag $requestDataBag, SalesChannelContext $context): ValidatePayload
    {
        return new ValidatePayload(
            $cart,
            $requestDataBag->all(),
            $context
        );
    }

    protected function buildCapturePayload(PreparedPaymentTransactionStruct $transaction, Struct $preOrderPaymentStruct): CapturePayload
    {
        return new CapturePayload(
            $transaction->getOrderTransaction(),
            $transaction->getOrder(),
            $preOrderPaymentStruct
        );
    }
}
