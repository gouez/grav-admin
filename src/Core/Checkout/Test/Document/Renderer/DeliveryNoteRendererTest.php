<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Document\Renderer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\SalesChannel\CartService;
use Laser\Core\Checkout\Document\Event\DeliveryNoteOrdersEvent;
use Laser\Core\Checkout\Document\FileGenerator\FileTypes;
use Laser\Core\Checkout\Document\Renderer\DeliveryNoteRenderer;
use Laser\Core\Checkout\Document\Renderer\DocumentRendererConfig;
use Laser\Core\Checkout\Document\Renderer\RenderedDocument;
use Laser\Core\Checkout\Document\Renderer\RendererResult;
use Laser\Core\Checkout\Document\Struct\DocumentGenerateOperation;
use Laser\Core\Checkout\Test\Document\DocumentTrait;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextService;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('customer-order')]
class DeliveryNoteRendererTest extends TestCase
{
    use DocumentTrait;

    private SalesChannelContext $salesChannelContext;

    private Context $context;

    private DeliveryNoteRenderer $deliveryNoteRenderer;

    private CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = Context::createDefaultContext();

        $priceRuleId = Uuid::randomHex();

        $this->salesChannelContext = $this->getContainer()->get(SalesChannelContextFactory::class)->create(
            Uuid::randomHex(),
            TestDefaults::SALES_CHANNEL,
            [
                SalesChannelContextService::CUSTOMER_ID => $this->createCustomer(),
            ]
        );

        $this->salesChannelContext->setRuleIds([$priceRuleId]);
        $this->deliveryNoteRenderer = $this->getContainer()->get(DeliveryNoteRenderer::class);
        $this->cartService = $this->getContainer()->get(CartService::class);
    }

    /**
     * @dataProvider deliveryNoteRendererDataProvider
     */
    public function testRender(string $deliveryNoteNumber, \Closure $assertionCallback): void
    {
        $cart = $this->generateDemoCart(3);

        $orderId = $this->cartService->order($cart, $this->salesChannelContext, new RequestDataBag());

        $operation = new DocumentGenerateOperation($orderId, FileTypes::PDF, [
            'documentNumber' => $deliveryNoteNumber,
            'itemsPerPage' => 2,
        ]);

        $caughtEvent = null;

        $this->getContainer()->get('event_dispatcher')
            ->addListener(DeliveryNoteOrdersEvent::class, function (DeliveryNoteOrdersEvent $event) use (&$caughtEvent): void {
                $caughtEvent = $event;
            });

        $processedTemplate = $this->deliveryNoteRenderer->render(
            [$orderId => $operation],
            $this->context,
            new DocumentRendererConfig()
        );

        static::assertInstanceOf(DeliveryNoteOrdersEvent::class, $caughtEvent);
        static::assertCount(1, $caughtEvent->getOperations());
        static::assertSame($operation, $caughtEvent->getOperations()[$orderId] ?? null);
        static::assertCount(1, $caughtEvent->getOrders());
        static::assertInstanceOf(RendererResult::class, $processedTemplate);
        static::assertArrayHasKey($orderId, $processedTemplate->getSuccess());
        $rendered = $processedTemplate->getSuccess()[$orderId];
        $order = $caughtEvent->getOrders()->get($orderId);
        static::assertNotNull($order);

        static::assertInstanceOf(RenderedDocument::class, $rendered);
        static::assertInstanceOf(DeliveryNoteOrdersEvent::class, $caughtEvent);
        static::assertCount(1, $caughtEvent->getOrders());
        static::assertStringContainsString('<html>', $rendered->getHtml());
        static::assertStringContainsString('</html>', $rendered->getHtml());

        $assertionCallback($deliveryNoteNumber, $order->getOrderNumber(), $rendered);
    }

    public static function deliveryNoteRendererDataProvider(): \Generator
    {
        yield 'render delivery_note successfully' => [
            '2000',
            function (string $deliveryNoteNumber, string $orderNumber, RenderedDocument $rendered): void {
                $html = $rendered->getHtml();
                static::assertStringContainsString('<html>', $html);
                static::assertStringContainsString('</html>', $html);

                static::assertStringContainsString('Delivery note ' . $deliveryNoteNumber, $html);
                static::assertStringContainsString(sprintf('Delivery note %s for Order %s ', $deliveryNoteNumber, $orderNumber), $html);
            },
        ];

        yield 'render delivery_note with document number' => [
            'DELIVERY_NOTE_9999',
            function (string $deliveryNoteNumber, string $orderNumber, RenderedDocument $rendered): void {
                static::assertEquals('DELIVERY_NOTE_9999', $rendered->getNumber());
                static::assertEquals('delivery_note_DELIVERY_NOTE_9999', $rendered->getName());

                static::assertStringContainsString("Delivery note $deliveryNoteNumber for Order $orderNumber", $rendered->getHtml());
                static::assertStringContainsString("Delivery note $deliveryNoteNumber for Order $orderNumber", $rendered->getHtml());
            },
        ];
    }

    public function testNotCreatingNewOrderVersionId(): void
    {
        $cart = $this->generateDemoCart(1);
        $orderId = $this->persistCart($cart);

        $operationDelivery = new DocumentGenerateOperation($orderId);

        static::assertEquals($operationDelivery->getOrderVersionId(), Defaults::LIVE_VERSION);

        $this->deliveryNoteRenderer->render(
            [$orderId => $operationDelivery],
            $this->context,
            new DocumentRendererConfig()
        );

        static::assertEquals($operationDelivery->getOrderVersionId(), Defaults::LIVE_VERSION);
    }
}
