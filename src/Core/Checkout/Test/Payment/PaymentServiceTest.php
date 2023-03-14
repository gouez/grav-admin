<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Payment;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Checkout\Payment\Cart\PaymentHandler\DefaultPayment;
use Laser\Core\Checkout\Payment\Cart\Token\JWTFactoryV2;
use Laser\Core\Checkout\Payment\Cart\Token\TokenStruct;
use Laser\Core\Checkout\Payment\Exception\InvalidOrderException;
use Laser\Core\Checkout\Payment\Exception\InvalidTokenException;
use Laser\Core\Checkout\Payment\Exception\TokenExpiredException;
use Laser\Core\Checkout\Payment\Exception\TokenInvalidatedException;
use Laser\Core\Checkout\Payment\PaymentMethodDefinition;
use Laser\Core\Checkout\Payment\PaymentService;
use Laser\Core\Checkout\Test\Cart\Common\Generator;
use Laser\Core\Checkout\Test\Payment\Handler\V630\AsyncTestPaymentHandler as AsyncTestPaymentHandlerV630;
use Laser\Core\Checkout\Test\Payment\Handler\V630\SyncTestPaymentHandler as SyncTestPaymentHandlerV630;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\BasicTestDataBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;
use Laser\Core\System\StateMachine\Loader\InitialStateIdLoader;
use Laser\Core\System\StateMachine\StateMachineDefinition;
use Laser\Core\Test\TestDefaults;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 * This test handles transactions itself, because it shuts down the kernel in the setUp method.
 */
#[Package('checkout')]
class PaymentServiceTest extends TestCase
{
    use KernelTestBehaviour;
    use BasicTestDataBehaviour;

    private PaymentService $paymentService;

    private JWTFactoryV2 $tokenFactory;

    private EntityRepository $orderRepository;

    private EntityRepository $customerRepository;

    private EntityRepository $orderTransactionRepository;

    private EntityRepository $paymentMethodRepository;

    private Context $context;

    private EntityRepository $stateMachineRepository;

    private EntityRepository $stateMachineStateRepository;

    protected function setUp(): void
    {
        // Previous tests may build the local cache of \Laser\Core\System\StateMachine\StateMachineRegistry, shutdown the Kernel to rebuild the container
        $this->getContainer()->get('kernel')->shutdown();

        $this->paymentService = $this->getContainer()->get(PaymentService::class);
        $this->tokenFactory = $this->getContainer()->get(JWTFactoryV2::class);
        $this->orderRepository = $this->getRepository(OrderDefinition::ENTITY_NAME);
        $this->customerRepository = $this->getRepository(CustomerDefinition::ENTITY_NAME);
        $this->orderTransactionRepository = $this->getRepository(OrderTransactionDefinition::ENTITY_NAME);
        $this->paymentMethodRepository = $this->getRepository(PaymentMethodDefinition::ENTITY_NAME);
        $this->stateMachineRepository = $this->getRepository(StateMachineDefinition::ENTITY_NAME);
        $this->stateMachineStateRepository = $this->getRepository(StateMachineStateDefinition::ENTITY_NAME);
        $this->context = Context::createDefaultContext();

        $this->getContainer()->get(Connection::class)->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->getContainer()
            ->get(Connection::class)
            ->rollBack();

        // Shutdown the Kernel, to clear the local cache of the \Laser\Core\System\StateMachine\StateMachineRegistry for following test cases.
        $this->getContainer()->get('kernel')->shutdown();
    }

    public function testHandlePaymentByOrderWithInvalidOrderId(): void
    {
        $orderId = Uuid::randomHex();
        $salesChannelContext = Generator::createSalesChannelContext();
        $this->expectException(InvalidOrderException::class);
        $this->expectExceptionMessage(sprintf('The order with id %s is invalid or could not be found.', $orderId));
        $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);
    }

    public function testHandlePaymentByOrderSyncPaymentV630(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context, SyncTestPaymentHandlerV630::class);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        static::assertNull($this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext));
    }

    public function testHandlePaymentByOrderAsyncPaymentV630(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        $response = $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);

        static::assertNotNull($response);
        static::assertEquals(AsyncTestPaymentHandlerV630::REDIRECT_URL, $response->getTargetUrl());
    }

    public function testHandlePaymentByOrderAsyncPaymentWithFinalizeV630(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $transactionId = $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        $response = $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);

        static::assertNotNull($response);
        static::assertEquals(AsyncTestPaymentHandlerV630::REDIRECT_URL, $response->getTargetUrl());

        $transaction = new OrderTransactionEntity();
        $transaction->setId($transactionId);
        $transaction->setPaymentMethodId($paymentMethodId);
        $transaction->setOrderId($orderId);
        $transaction->setStateId(Uuid::randomHex());
        $tokenStruct = new TokenStruct(null, null, $transaction->getPaymentMethodId(), $transaction->getId(), 'testFinishUrl');
        $token = $this->tokenFactory->generateToken($tokenStruct);
        $request = new Request();
        $tokenStruct = $this->paymentService->finalizeTransaction($token, $request, $salesChannelContext);

        static::assertSame('testFinishUrl', $tokenStruct->getFinishUrl());
        $criteria = new Criteria([$transactionId]);
        $criteria->addAssociation('stateMachineState');
        $transactionEntity = $this->orderTransactionRepository->search($criteria, $this->context)->first();
        static::assertSame(
            OrderTransactionStates::STATE_PAID,
            $transactionEntity->getStateMachineState()->getTechnicalName()
        );
    }

    public function testDuplicateFinalizeCall(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $transactionId = $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        $response = $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);

        static::assertNotNull($response);
        static::assertEquals(AsyncTestPaymentHandlerV630::REDIRECT_URL, $response->getTargetUrl());

        $transaction = new OrderTransactionEntity();
        $transaction->setId($transactionId);
        $transaction->setPaymentMethodId($paymentMethodId);
        $transaction->setOrderId($orderId);
        $transaction->setStateId(Uuid::randomHex());

        $tokenStruct = new TokenStruct(null, null, $transaction->getPaymentMethodId(), $transaction->getId(), 'testFinishUrl');
        $token = $this->tokenFactory->generateToken($tokenStruct);

        static::expectException(TokenInvalidatedException::class);

        $this->paymentService->finalizeTransaction($token, new Request(), $salesChannelContext);
        $this->paymentService->finalizeTransaction($token, new Request(), $salesChannelContext);
    }

    public function testHandlePaymentByOrderDefaultPaymentV630(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context, DefaultPayment::class);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        static::assertNull($this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext));
    }

    public function testFinalizeTransactionWithInvalidToken(): void
    {
        $token = Uuid::randomHex();
        $request = new Request();
        $this->expectException(InvalidTokenException::class);

        $paymentMethodId = $this->createPaymentMethodV630($this->context, DefaultPayment::class);

        $this->paymentService->finalizeTransaction($token, $request, $this->getSalesChannelContext($paymentMethodId));
    }

    public function testFinalizeTransactionWithExpiredToken(): void
    {
        $request = new Request();
        $transaction = new OrderTransactionEntity();
        $transaction->setId(Uuid::randomHex());
        $transaction->setOrderId(Uuid::randomHex());
        $transaction->setPaymentMethodId(Uuid::randomHex());
        $transaction->setStateId(Uuid::randomHex());
        $tokenStruct = new TokenStruct(null, null, $transaction->getPaymentMethodId(), $transaction->getId(), null, -1);
        $token = $this->tokenFactory->generateToken($tokenStruct);

        $paymentMethodId = $this->createPaymentMethodV630($this->context, DefaultPayment::class);

        $response = $this->paymentService->finalizeTransaction($token, $request, $this->getSalesChannelContext($paymentMethodId));
        static::assertInstanceof(TokenExpiredException::class, $response->getException());
    }

    public function testFinalizeTransactionCustomerCanceledV630(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);
        $transactionId = $this->createTransaction($orderId, $paymentMethodId, $this->context);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);

        $response = $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);

        static::assertNotNull($response);
        static::assertEquals(AsyncTestPaymentHandlerV630::REDIRECT_URL, $response->getTargetUrl());

        $transaction = new OrderTransactionEntity();
        $transaction->setId($transactionId);
        $transaction->setPaymentMethodId($paymentMethodId);
        $transaction->setOrderId($orderId);
        $transaction->setStateId(Uuid::randomHex());
        $tokenStruct = new TokenStruct(null, null, $transaction->getPaymentMethodId(), $transaction->getId(), 'testFinishUrl');
        $token = $this->tokenFactory->generateToken($tokenStruct);
        $request = new Request();
        $request->query->set('cancel', '1');

        $response = $this->paymentService->finalizeTransaction($token, $request, $this->getSalesChannelContext($paymentMethodId));

        static::assertNotNull($response);
        static::assertNotEmpty($response->getException());

        $criteria = new Criteria([$transactionId]);
        $criteria->addAssociation('stateMachineState');

        $transactionEntity = $this->orderTransactionRepository->search($criteria, $this->context)->first();

        static::assertSame(
            OrderTransactionStates::STATE_CANCELLED,
            $transactionEntity->getStateMachineState()->getTechnicalName()
        );

        //can fail again
        $token = $this->tokenFactory->generateToken($tokenStruct);
        $response = $this->paymentService->finalizeTransaction($token, $request, $this->getSalesChannelContext($paymentMethodId));

        static::assertNotEmpty($response->getException());

        $criteria = new Criteria([$transactionId]);
        $criteria->addAssociation('stateMachineState');

        $transactionEntity = $this->orderTransactionRepository->search($criteria, $this->context)->first();

        static::assertSame(
            OrderTransactionStates::STATE_CANCELLED,
            $transactionEntity->getStateMachineState()->getTechnicalName()
        );

        //can success after cancelled
        $request->query->set('cancel', '0');
        $token = $this->tokenFactory->generateToken($tokenStruct);
        $this->paymentService->finalizeTransaction($token, $request, $this->getSalesChannelContext($paymentMethodId));

        $criteria = new Criteria([$transactionId]);
        $criteria->addAssociation('stateMachineState');

        $transactionEntity = $this->orderTransactionRepository->search($criteria, $this->context)->first();

        static::assertSame(
            OrderTransactionStates::STATE_PAID,
            $transactionEntity->getStateMachineState()->getTechnicalName()
        );
    }

    public function testHandlePaymentByOrderCanHandleNoneOpenInitialTransactionState(): void
    {
        $paymentMethodId = $this->createPaymentMethodV630($this->context);
        $customerId = $this->createCustomer($this->context);
        $orderId = $this->createOrder($customerId, $paymentMethodId, $this->context);

        // Set initialStateId to reminded
        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->addFilter(
            new EqualsFilter('technicalName', OrderTransactionStates::STATE_MACHINE)
        );

        // We can not use the state machine registry here because it would cache the result with the open initial state
        $orderTransactionStateMachineId = $this->stateMachineRepository->searchIds($criteria, $this->context)->firstId();
        static::assertNotNull($orderTransactionStateMachineId);

        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->addFilter(
            new EqualsFilter('stateMachineId', $orderTransactionStateMachineId),
            new EqualsFilter('technicalName', OrderTransactionStates::STATE_REMINDED)
        );

        $remindedStateId = $this->stateMachineStateRepository->searchIds($criteria, $this->context)->firstId();
        static::assertNotNull($remindedStateId);

        $this->stateMachineRepository->update(
            [
                [
                    'id' => $orderTransactionStateMachineId,
                    'initialStateId' => $remindedStateId,
                ],
            ],
            $this->context
        );

        $transactionId = $this->createTransaction($orderId, $paymentMethodId, $this->context);
        $transaction = $this->orderTransactionRepository->search(new Criteria([$transactionId]), $this->context)->first();
        static::assertNotNull($transaction);
        static::assertSame($transaction->getStateId(), $remindedStateId);

        $salesChannelContext = $this->getSalesChannelContext($paymentMethodId);
        $response = $this->paymentService->handlePaymentByOrder($orderId, new RequestDataBag(), $salesChannelContext);

        static::assertNotNull($response);
        static::assertEquals(AsyncTestPaymentHandlerV630::REDIRECT_URL, $response->getTargetUrl());
    }

    private function getSalesChannelContext(string $paymentMethodId): SalesChannelContext
    {
        return $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL, [
                SalesChannelContextService::PAYMENT_METHOD_ID => $paymentMethodId,
            ]);
    }

    private function createTransaction(
        string $orderId,
        string $paymentMethodId,
        Context $context
    ): string {
        $id = Uuid::randomHex();
        $transaction = [
            'id' => $id,
            'orderId' => $orderId,
            'paymentMethodId' => $paymentMethodId,
            'stateId' => $this->getInitialOrderTransactionStateId(),
            'amount' => new CalculatedPrice(100, 100, new CalculatedTaxCollection(), new TaxRuleCollection(), 1),
            'payload' => '{}',
        ];

        $this->orderTransactionRepository->upsert([$transaction], $context);

        return $id;
    }

    private function createOrder(
        string $customerId,
        string $paymentMethodId,
        Context $context
    ): string {
        $orderId = Uuid::randomHex();
        $addressId = Uuid::randomHex();
        $stateId = $this->getInitialOrderTransactionStateId();

        $order = [
            'id' => $orderId,
            'itemRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            'totalRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            'orderNumber' => Uuid::randomHex(),
            'orderDateTime' => (new \DateTimeImmutable())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'price' => new CartPrice(10, 10, 10, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_NET),
            'shippingCosts' => new CalculatedPrice(10, 10, new CalculatedTaxCollection(), new TaxRuleCollection()),
            'orderCustomer' => [
                'customerId' => $customerId,
                'email' => 'test@example.com',
                'salutationId' => $this->getValidSalutationId(),
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
            ],
            'stateId' => $stateId,
            'paymentMethodId' => $paymentMethodId,
            'currencyId' => Defaults::CURRENCY,
            'currencyFactor' => 1.0,
            'salesChannelId' => TestDefaults::SALES_CHANNEL,
            'billingAddressId' => $addressId,
            'addresses' => [
                [
                    'id' => $addressId,
                    'salutationId' => $this->getValidSalutationId(),
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Ebbinghoff 10',
                    'zipcode' => '48624',
                    'city' => 'Schöppingen',
                    'countryId' => $this->getValidCountryId(),
                ],
            ],
            'lineItems' => [],
            'deliveries' => [],
            'context' => '{}',
            'payload' => '{}',
        ];

        $this->orderRepository->upsert([$order], $context);

        return $orderId;
    }

    private function createCustomer(Context $context): string
    {
        $customerId = Uuid::randomHex();
        $addressId = Uuid::randomHex();

        $customer = [
            'id' => $customerId,
            'customerNumber' => '1337',
            'salutationId' => $this->getValidSalutationId(),
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'email' => Uuid::randomHex() . '@example.com',
            'password' => 'laser',
            'defaultPaymentMethodId' => $this->getValidPaymentMethodId(),
            'groupId' => TestDefaults::FALLBACK_CUSTOMER_GROUP,
            'salesChannelId' => TestDefaults::SALES_CHANNEL,
            'defaultBillingAddressId' => $addressId,
            'defaultShippingAddressId' => $addressId,
            'addresses' => [
                [
                    'id' => $addressId,
                    'customerId' => $customerId,
                    'countryId' => $this->getValidCountryId(),
                    'salutationId' => $this->getValidSalutationId(),
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Ebbinghoff 10',
                    'zipcode' => '48624',
                    'city' => 'Schöppingen',
                ],
            ],
        ];

        $this->customerRepository->upsert([$customer], $context);

        return $customerId;
    }

    private function createPaymentMethodV630(
        Context $context,
        string $handlerIdentifier = AsyncTestPaymentHandlerV630::class
    ): string {
        $id = Uuid::randomHex();
        $payment = [
            'id' => $id,
            'handlerIdentifier' => $handlerIdentifier,
            'name' => 'Test Payment',
            'description' => 'Test payment handler',
            'active' => true,
        ];

        $this->paymentMethodRepository->upsert([$payment], $context);

        return $id;
    }

    private function getRepository(string $entityName): EntityRepository
    {
        $repository = $this->getContainer()->get(\sprintf('%s.repository', $entityName));
        static::assertInstanceOf(EntityRepository::class, $repository);

        return $repository;
    }

    /**
     * Does the same like \Laser\Core\System\StateMachine\StateMachineRegistry::getInitialState without local caching.
     */
    private function getInitialOrderTransactionStateId(): string
    {
        $this->getContainer()->get(InitialStateIdLoader::class)->reset();

        return $this->getContainer()->get(InitialStateIdLoader::class)
            ->get(OrderTransactionStates::STATE_MACHINE);
    }
}
