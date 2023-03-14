<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Order;

use Faker\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Order\OrderConverter;
use Laser\Core\Checkout\Cart\Order\OrderPersister;
use Laser\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Processor;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\SalesChannelEntity;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
class OrderPersisterTest extends TestCase
{
    use IntegrationTestBehaviour;

    private OrderPersister $orderPersister;

    private Processor $cartProcessor;

    private OrderConverter $orderConverter;

    protected function setUp(): void
    {
        $this->orderPersister = $this->getContainer()->get(OrderPersister::class);
        $this->cartProcessor = $this->getContainer()->get(Processor::class);
        $this->orderConverter = $this->getContainer()->get(OrderConverter::class);
    }

    public function testSave(): void
    {
        $cart = new Cart(Uuid::randomHex());
        $cart->add(
            (new LineItem('test', 'test'))
                ->setPrice(new CalculatedPrice(1, 1, new CalculatedTaxCollection(), new TaxRuleCollection()))
                ->setLabel('test')
        )->add(
            (new LineItem('test2', 'test'))
                ->setPrice(new CalculatedPrice(1, 1, new CalculatedTaxCollection(), new TaxRuleCollection()))
                ->setLabel('test2')
        );
        $positionByIdentifier = [
            'test' => 1,
            'test2' => 2,
        ];

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects(static::once())
            ->method('create')
            ->with(
                static::callback(function (array $payload) use ($positionByIdentifier) {
                    foreach ($payload[0]['lineItems'] as $lineItem) {
                        if ($positionByIdentifier[$lineItem['identifier']] !== $lineItem['position']) {
                            return false;
                        }
                    }

                    return true;
                })
            );
        $order = new OrderEntity();
        $order->setUniqueIdentifier(Uuid::randomHex());
        $repository->method('search')->willReturn(
            new EntitySearchResult(
                'order',
                1,
                new EntityCollection([$order]),
                null,
                new Criteria(),
                Context::createDefaultContext()
            )
        );

        $persister = new OrderPersister($repository, $this->orderConverter);

        $persister->persist($cart, $this->getSalesChannelContext());
    }

    public function testSaveWithMissingLabel(): void
    {
        $cart = new Cart('a-b-c');
        $cart->add(
            (new LineItem('test', LineItem::CREDIT_LINE_ITEM_TYPE))
                ->setPriceDefinition(new AbsolutePriceDefinition(1))
        );

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);

        $processedCart = $this->cartProcessor->process($cart, $context, new CartBehavior());

        $exception = null;

        try {
            $this->orderPersister->persist($processedCart, $context);
        } catch (CartException $exception) {
        }

        static::assertInstanceOf(CartException::class, $exception);
        static::assertStringContainsString('Line item "test" incomplete. Property "label" missing.', $exception->getMessage());
    }

    private function getCustomer(): CustomerEntity
    {
        $faker = Factory::create();

        $billingAddress = new CustomerAddressEntity();
        $billingAddress->setId('SWAG-ADDRESS-ID-1');
        $billingAddress->setSalutationId($this->getValidSalutationId());
        $billingAddress->setFirstName($faker->firstName);
        $billingAddress->setLastName($faker->lastName);
        $billingAddress->setStreet($faker->streetAddress);
        $billingAddress->setZipcode($faker->postcode);
        $billingAddress->setCity($faker->city);
        $billingAddress->setCountryId('SWAG-AREA-COUNTRY-ID-1');

        $customer = new CustomerEntity();
        $customer->setId('SWAG-CUSTOMER-ID-1');
        $customer->setDefaultBillingAddress($billingAddress);
        $customer->setEmail('test@example.com');
        $customer->setSalutationId($this->getValidSalutationId());
        $customer->setFirstName($faker->firstName);
        $customer->setLastName($faker->lastName);
        $customer->setCustomerNumber('Test');

        return $customer;
    }

    private function getSalesChannelContext(): MockObject&SalesChannelContext
    {
        $customer = $this->getCustomer();
        $salesChannel = new SalesChannelEntity();
        $salesChannel->setLanguageId(Defaults::LANGUAGE_SYSTEM);
        $salesChannelContext = $this->createMock(SalesChannelContext::class);
        $salesChannelContext->method('getCustomer')->willReturn($customer);

        $context = Context::createDefaultContext();
        $salesChannel->setId(TestDefaults::SALES_CHANNEL);
        $salesChannelContext->method('getSalesChannel')->willReturn($salesChannel);
        $salesChannelContext->method('getContext')->willReturn($context);

        return $salesChannelContext;
    }
}
