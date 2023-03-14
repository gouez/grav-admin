<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Webhook;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\ArrayBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\CollectionBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\EntityBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\InvalidAvailableDataBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\InvalidTypeBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\NestedEntityBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\ScalarBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\StructuredArrayObjectBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\StructuredObjectBusinessEvent;
use Laser\Core\Framework\Test\Webhook\_fixtures\BusinessEvents\UnstructuredObjectBusinessEvent;
use Laser\Core\Framework\Webhook\BusinessEventEncoder;
use Laser\Core\System\Tax\TaxCollection;
use Laser\Core\System\Tax\TaxEntity;

/**
 * @internal
 */
class BusinessEventEncoderTest extends TestCase
{
    use IntegrationTestBehaviour;

    private BusinessEventEncoder $businessEventEncoder;

    public function setUp(): void
    {
        $this->businessEventEncoder = $this->getContainer()->get(BusinessEventEncoder::class);
    }

    /**
     * @dataProvider getEvents
     */
    public function testScalarEvents(FlowEventAware $event): void
    {
        $laserVersion = $this->getContainer()->getParameter('kernel.laser_version');
        static::assertTrue(
            method_exists($event, 'getEncodeValues'),
            'Event does not have method getEncodeValues'
        );
        static::assertEquals($event->getEncodeValues($laserVersion), $this->businessEventEncoder->encode($event));
    }

    public static function getEvents(): \Generator
    {
        $tax = new TaxEntity();
        $tax->setId('tax-id');
        $tax->setName('test');
        $tax->setTaxRate(19);
        $tax->setPosition(1);

        yield 'ScalarBusinessEvent' => [new ScalarBusinessEvent()];
        yield 'StructuredObjectBusinessEvent' => [new StructuredObjectBusinessEvent()];
        yield 'StructuredArrayObjectBusinessEvent' => [new StructuredArrayObjectBusinessEvent()];
        yield 'UnstructuredObjectBusinessEvent' => [new UnstructuredObjectBusinessEvent()];
        yield 'EntityBusinessEvent' => [new EntityBusinessEvent($tax)];
        yield 'CollectionBusinessEvent' => [new CollectionBusinessEvent(new TaxCollection([$tax]))];
        yield 'ArrayBusinessEvent' => [new ArrayBusinessEvent(new TaxCollection([$tax]))];
        yield 'NestedEntityBusinessEvent' => [new NestedEntityBusinessEvent($tax)];
    }

    public function testInvalidType(): void
    {
        static::expectException(\RuntimeException::class);
        $this->businessEventEncoder->encode(new InvalidTypeBusinessEvent());
    }

    public function testInvalidAvailableData(): void
    {
        static::expectException(\RuntimeException::class);
        $this->businessEventEncoder->encode(new InvalidAvailableDataBusinessEvent());
    }
}
