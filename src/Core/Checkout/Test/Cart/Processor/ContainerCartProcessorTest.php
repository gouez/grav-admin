<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\LineItem\CartDataCollection;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Processor\ContainerCartProcessor;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTax;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\AbsoluteItem;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\CalculatedTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\ContainerItem;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\HighTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\LowTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\PercentageItem;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\QuantityItem;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('checkout')]
class ContainerCartProcessorTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @dataProvider calculationProvider
     */
    public function testCalculation(LineItem $item, ?CalculatedPrice $expected): void
    {
        $processor = $this->getContainer()->get(ContainerCartProcessor::class);

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);

        $cart = new Cart('test');
        $cart->setLineItems(new LineItemCollection([$item]));

        $new = new Cart('after');
        $processor->process(new CartDataCollection(), $cart, $new, $context, new CartBehavior());

        if ($expected === null) {
            static::assertFalse($new->has($item->getId()));

            return;
        }

        static::assertTrue($new->has($item->getId()));

        static::assertInstanceOf(CalculatedPrice::class, $item->getPrice());
        static::assertEquals($expected->getUnitPrice(), $item->getPrice()->getUnitPrice());
        static::assertEquals($expected->getTotalPrice(), $item->getPrice()->getTotalPrice());
        static::assertEquals($expected->getCalculatedTaxes()->getAmount(), $item->getPrice()->getCalculatedTaxes()->getAmount());

        foreach ($expected->getCalculatedTaxes() as $tax) {
            $actual = $item->getPrice()->getCalculatedTaxes()->get((string) $tax->getTaxRate());

            static::assertInstanceOf(CalculatedTax::class, $actual, sprintf('Missing tax for rate %s', $tax->getTaxRate()));
            static::assertEquals($tax->getTax(), $actual->getTax());
        }

        foreach ($item->getPrice()->getCalculatedTaxes() as $tax) {
            $actual = $expected->getCalculatedTaxes()->get((string) $tax->getTaxRate());

            static::assertInstanceOf(CalculatedTax::class, $actual, sprintf('Missing tax for rate %s', $tax->getTaxRate()));
            static::assertEquals($tax->getTax(), $actual->getTax());
        }
    }

    public static function calculationProvider(): \Generator
    {
        yield 'Test empty container will be removed' => [
            new ContainerItem(),
            null,
        ];

        yield 'Test container with one quantity price definition' => [
            new ContainerItem([
                new QuantityItem(20, new HighTaxes()),
            ]),
            new CalculatedPrice(20, 20, new CalculatedTaxes([19 => 3.19]), new HighTaxes()),
        ];

        yield 'Test percentage discount for one item' => [
            new ContainerItem([
                new QuantityItem(20, new HighTaxes()),
                new PercentageItem(-10),
            ]),
            new CalculatedPrice(18, 18, new CalculatedTaxes([19 => 2.87]), new HighTaxes()),
        ];

        yield 'Test absolute discount for one item' => [
            new ContainerItem([
                new QuantityItem(20, new HighTaxes()),
                new AbsoluteItem(-10),
            ]),
            new CalculatedPrice(10, 10, new CalculatedTaxes([19 => 1.59]), new HighTaxes()),
        ];

        yield 'Test discount calculation for two items' => [
            new ContainerItem([
                new QuantityItem(20, new HighTaxes()),
                new QuantityItem(20, new LowTaxes()),
                new PercentageItem(-10),
            ]),
            new CalculatedPrice(36, 36, new CalculatedTaxes([19 => 2.87, 7 => 1.18]), new HighTaxes()),
        ];

        yield 'Test discount calculation with random order' => [
            new ContainerItem([
                new QuantityItem(20, new LowTaxes()),
                new PercentageItem(-10),
                new QuantityItem(20, new HighTaxes()),
            ]),
            new CalculatedPrice(36, 36, new CalculatedTaxes([19 => 2.87, 7 => 1.18]), new HighTaxes()),
        ];

        yield 'Test nested calculation' => [
            new ContainerItem([ // 108,40€ - 10% = 97,56€
                new QuantityItem(20, new HighTaxes()),
                new QuantityItem(20, new LowTaxes()),
                new PercentageItem(-10),

                new ContainerItem([ // 76€ - 10% = 68,40€
                    new QuantityItem(20, new HighTaxes()),
                    new QuantityItem(20, new LowTaxes()),

                    new ContainerItem([                             // 40 - 10% = 36€
                        new QuantityItem(20, new HighTaxes()),
                        new QuantityItem(20, new LowTaxes()),
                        new PercentageItem(-10),
                    ]),
                    new PercentageItem(-10),
                ]),
            ]),
            new CalculatedPrice(97.56, 97.56, new CalculatedTaxes([19 => 7.77, 7 => 3.20]), new HighTaxes()),
        ];
    }
}
