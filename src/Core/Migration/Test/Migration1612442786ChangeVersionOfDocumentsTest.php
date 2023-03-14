<?php declare(strict_types=1);

namespace Laser\Core\Migration\Test;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\LineItemFactoryHandler\ProductLineItemFactory;
use Laser\Core\Checkout\Cart\Order\OrderPersister;
use Laser\Core\Checkout\Cart\PriceDefinitionFactory;
use Laser\Core\Checkout\Cart\Processor;
use Laser\Core\Checkout\Cart\SalesChannel\CartService;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Document\DocumentEntity;
use Laser\Core\Checkout\Document\Renderer\DeliveryNoteRenderer;
use Laser\Core\Checkout\Document\Service\DocumentGenerator;
use Laser\Core\Checkout\Document\Struct\DocumentGenerateOperation;
use Laser\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\EntityWriter;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\BasicTestDataBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\CountryAddToSalesChannelTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\TaxAddToSalesChannelTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Migration\V6_4\Migration1612442786ChangeVersionOfDocuments;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 * NEXT-21735 - Not deterministic due to SalesChannelContextFactory
 *
 * @group not-deterministic
 */
#[Package('core')]
class Migration1612442786ChangeVersionOfDocumentsTest extends TestCase
{
    use BasicTestDataBehaviour;
    use CountryAddToSalesChannelTestBehaviour;
    use IntegrationTestBehaviour;
    use KernelTestBehaviour;
    use TaxAddToSalesChannelTestBehaviour;

    private SalesChannelContext $salesChannelContext;

    private Context $context;

    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->getContainer()->get(Connection::class);

        $this->context = Context::createDefaultContext();

        $paymentMethod = $this->getAvailablePaymentMethod();

        $customerId = $this->createCustomer($paymentMethod->getId());
        $shippingMethod = $this->getAvailableShippingMethod();

        $this->addCountriesToSalesChannel();

        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)->create(
            Uuid::randomHex(),
            TestDefaults::SALES_CHANNEL,
            [
                SalesChannelContextService::CUSTOMER_ID => $customerId,
                SalesChannelContextService::SHIPPING_METHOD_ID => $shippingMethod->getId(),
                SalesChannelContextService::PAYMENT_METHOD_ID => $paymentMethod->getId(),
            ]
        );

        $ruleIds = [$shippingMethod->getAvailabilityRuleId()];
        if ($paymentRuleId = $paymentMethod->getAvailabilityRuleId()) {
            $ruleIds[] = $paymentRuleId;
        }
        $this->salesChannelContext->setRuleIds($ruleIds);
    }

    public function testMigrationWorks(): void
    {
        $cart = $this->generateDemoCart(2);
        $orderId = $this->persistCart($cart);

        $documentGenerator = $this->getContainer()->get(DocumentGenerator::class);
        $operation = new DocumentGenerateOperation($orderId);
        $result = $documentGenerator->generate(DeliveryNoteRenderer::TYPE, [$orderId => $operation], $this->context)->getSuccess();

        $documentStruct = $result->first();

        static::assertNotNull($documentStruct);
        static::assertTrue(Uuid::isValid($documentStruct->getId()));

        // Set Document to Live Version
        $documentRepository = $this->getContainer()->get('document.repository');

        $documentRepository
            ->update(
                [
                    [
                        'id' => $documentStruct->getId(),
                        'orderVersionId' => Defaults::LIVE_VERSION,
                    ],
                ],
                $this->context
            );

        $migration = new Migration1612442786ChangeVersionOfDocuments();
        $migration->update($this->connection);

        /** @var DocumentEntity $document */
        $document = $documentRepository->search(new Criteria([$documentStruct->getId()]), $this->context)->first();

        static::assertEquals(Defaults::LIVE_VERSION, $document->getOrderVersionId());
    }

    /**
     * @throws CartException
     * @throws \Exception
     */
    private function generateDemoCart(int $lineItemCount): Cart
    {
        $cart = new Cart('a-b-c');

        $keywords = ['awesome', 'epic', 'high quality'];

        $products = [];

        $factory = new ProductLineItemFactory(new PriceDefinitionFactory());

        for ($i = 0; $i < $lineItemCount; ++$i) {
            $id = Uuid::randomHex();

            $price = random_int(100, 200000) / 100.0;

            shuffle($keywords);
            $name = ucfirst(implode(' ', $keywords) . ' product');

            $products[] = [
                'id' => $id,
                'name' => $name,
                'price' => [
                    ['currencyId' => Defaults::CURRENCY, 'gross' => $price, 'net' => $price, 'linked' => false],
                ],
                'productNumber' => Uuid::randomHex(),
                'manufacturer' => ['id' => $id, 'name' => 'test'],
                'tax' => ['id' => $id, 'taxRate' => 19, 'name' => 'test'],
                'stock' => 10,
                'active' => true,
                'visibilities' => [
                    ['salesChannelId' => TestDefaults::SALES_CHANNEL, 'visibility' => ProductVisibilityDefinition::VISIBILITY_ALL],
                ],
            ];

            $cart->add($factory->create(['id' => $id, 'referencedId' => $id], $this->salesChannelContext));
            $this->addTaxDataToSalesChannel($this->salesChannelContext, end($products)['tax']);
        }

        $this->getContainer()->get('product.repository')
            ->create($products, Context::createDefaultContext());

        $cart = $this->getContainer()->get(Processor::class)->process($cart, $this->salesChannelContext, new CartBehavior());

        return $cart;
    }

    private function persistCart(Cart $cart): string
    {
        $cart = $this->getContainer()->get(CartService::class)->recalculate($cart, $this->salesChannelContext);
        $orderId = $this->getContainer()->get(OrderPersister::class)->persist($cart, $this->salesChannelContext);

        return $orderId;
    }

    private function createCustomer(string $paymentMethodId): string
    {
        $customerId = Uuid::randomHex();
        $addressId = Uuid::randomHex();

        $customer = [
            'id' => $customerId,
            'number' => '1337',
            'salutationId' => $this->getValidSalutationId(),
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'customerNumber' => '1337',
            'languageId' => Defaults::LANGUAGE_SYSTEM,
            'email' => Uuid::randomHex() . '@example.com',
            'password' => 'laser',
            'defaultPaymentMethodId' => $paymentMethodId,
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

        $this->getContainer()
            ->get(EntityWriter::class)
            ->upsert(
                $this->getContainer()->get(CustomerDefinition::class),
                [$customer],
                WriteContext::createFromContext($this->context)
            );

        return $customerId;
    }
}
