<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Document\Service;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Document\Renderer\InvoiceRenderer;
use Laser\Core\Checkout\Document\Service\ReferenceInvoiceLoader;
use Laser\Core\Checkout\Test\Document\DocumentTrait;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('customer-order')]
class ReferenceInvoiceLoaderTest extends TestCase
{
    use DocumentTrait;

    private ReferenceInvoiceLoader $referenceInvoiceLoader;

    private Context $context;

    private SalesChannelContext $salesChannelContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->referenceInvoiceLoader = $this->getContainer()->get(ReferenceInvoiceLoader::class);
        $this->context = Context::createDefaultContext();
        $customerId = $this->createCustomer();

        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)->create(
            Uuid::randomHex(),
            TestDefaults::SALES_CHANNEL,
            [
                SalesChannelContextService::CUSTOMER_ID => $customerId,
            ]
        );
    }

    public function testLoadWithoutDocument(): void
    {
        $this->getContainer()->get(Connection::class)->executeStatement('DELETE FROM `document`');

        $cart = $this->generateDemoCart(2);
        $orderId = $this->persistCart($cart);

        $result = $this->getContainer()->get(Connection::class)->fetchAllAssociative('SELECT * FROM `document`');
        $invoice = $this->referenceInvoiceLoader->load($orderId);

        static::assertEmpty($invoice);
    }

    public function testLoadWithoutReferenceDocumentId(): void
    {
        $cart = $this->generateDemoCart(2);
        $orderId = $this->persistCart($cart);

        // Create two documents, the latest invoice will be returned
        $this->createDocument(InvoiceRenderer::TYPE, $orderId, [], $this->context)->first();
        $invoiceStruct = $this->createDocument(InvoiceRenderer::TYPE, $orderId, [], $this->context)->first();
        static::assertNotNull($invoiceStruct);
        $invoice = $this->referenceInvoiceLoader->load($orderId);

        static::assertNotEmpty($invoice['id']);
        static::assertEquals($invoiceStruct->getId(), $invoice['id']);
    }

    public function testLoadWithReferenceDocumentId(): void
    {
        $cart = $this->generateDemoCart(2);
        $orderId = $this->persistCart($cart);

        // Create two documents, the one with passed referenceInvoiceId will be returned
        $invoiceStruct = $this->createDocument(InvoiceRenderer::TYPE, $orderId, [], $this->context)->first();
        static::assertNotNull($invoiceStruct);
        $this->createDocument(InvoiceRenderer::TYPE, $orderId, [], $this->context)->first();

        $invoice = $this->referenceInvoiceLoader->load($orderId, $invoiceStruct->getId());

        static::assertEquals($invoiceStruct->getId(), $invoice['id']);
        static::assertEquals($orderId, $invoice['orderId']);
        static::assertNotEquals(Defaults::LIVE_VERSION, $invoice['orderVersionId']);
        static::assertNotEmpty($invoice['config']);
    }
}
