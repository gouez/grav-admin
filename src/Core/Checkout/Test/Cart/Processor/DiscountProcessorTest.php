<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Processor;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\CartBehavior;
use Laser\Core\Checkout\Cart\LineItem\CartDataCollection;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Price\Struct\CartPrice;
use Laser\Core\Checkout\Cart\Processor\DiscountCartProcessor;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTax;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Checkout\Test\Cart\Common\Generator;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\AbsoluteItem;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\CalculatedItem;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\CalculatedTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\HighTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\LowTaxes;
use Laser\Core\Checkout\Test\Cart\Processor\_fixtures\PercentageItem;
use Laser\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Laser\Core\Test\TestDefaults;

/**
 * @internal
 */
#[Package('checkout')]
class DiscountProcessorTest extends TestCase
{
    use IntegrationTestBehaviour;

    final public const DISCOUNT_ID = 'discount-id';

    /**
     * @param array<LineItem> $items
     *
     * @dataProvider processorProvider
     */
    public function testProcessor(array $items, ?CalculatedPrice $expected): void
    {
        $processor = $this->getContainer()->get(DiscountCartProcessor::class);

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);

        $cart = new Cart('test');
        $cart->setLineItems(new LineItemCollection($items));

        $new = new Cart('after');
        $new->setLineItems(
            (new LineItemCollection($items))->filter(fn (LineItem $item) => $item->getType() !== LineItem::DISCOUNT_LINE_ITEM)
        );

        $processor->process(new CartDataCollection(), $cart, $new, $context, new CartBehavior());

        if ($expected === null) {
            static::assertFalse($new->has(self::DISCOUNT_ID));

            return;
        }

        static::assertTrue($new->has(self::DISCOUNT_ID));

        $item = $new->get(self::DISCOUNT_ID);
        static::assertInstanceOf(LineItem::class, $item);
        $price = $item->getPrice();

        static::assertInstanceOf(CalculatedPrice::class, $price);
        static::assertEquals($expected->getUnitPrice(), $price->getUnitPrice());
        static::assertEquals($expected->getTotalPrice(), $price->getTotalPrice());

        $taxes = $expected->getCalculatedTaxes();
        static::assertInstanceOf(CalculatedTaxCollection::class, $taxes);

        static::assertEquals($taxes->getAmount(), $price->getCalculatedTaxes()->getAmount());

        foreach ($taxes as $tax) {
            $actual = $price->getCalculatedTaxes()->get((string) $tax->getTaxRate());

            static::assertInstanceOf(CalculatedTax::class, $actual, sprintf('Missing tax for rate %s', $tax->getTaxRate()));
            static::assertEquals($tax->getTax(), $actual->getTax());
        }

        static::assertInstanceOf(LineItem::class, $item);
        static::assertInstanceOf(CalculatedPrice::class, $price);
        foreach ($price->getCalculatedTaxes() as $tax) {
            $actual = $taxes->get((string) $tax->getTaxRate());

            static::assertInstanceOf(CalculatedTax::class, $actual, sprintf('Missing tax for rate %s', $tax->getTaxRate()));
            static::assertEquals($tax->getTax(), $actual->getTax());
        }
    }

    public static function processorProvider(): \Generator
    {
        $context = Generator::createSalesChannelContext();
        $context->setTaxState(CartPrice::TAX_STATE_GROSS);
        $context->setItemRounding(new CashRoundingConfig(2, 0.01, true));

        yield 'Remove discounts when cart is empty' => [
            [new PercentageItem(10, self::DISCOUNT_ID)],
            null,
        ];

        yield 'Remove discount when cart gets negative' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new AbsoluteItem(-20, self::DISCOUNT_ID),
            ],
            null,
        ];

        yield 'Remove second discount when cart gets negative' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new AbsoluteItem(-5),
                new AbsoluteItem(-6, self::DISCOUNT_ID),
            ],
            null,
        ];

        yield 'Remove second discount when cart gets negative and check price' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new AbsoluteItem(-5, self::DISCOUNT_ID),
                new AbsoluteItem(-5),
            ],
            new CalculatedPrice(-5, -5, new CalculatedTaxes([19 => -0.8]), new TaxRuleCollection()),
        ];

        yield 'Calculate discount for one item' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new PercentageItem(-10, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-1, -1, new CalculatedTaxes([19 => -0.16]), new TaxRuleCollection()),
        ];

        yield 'Calculate absolute discount' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new AbsoluteItem(-1, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-1, -1, new CalculatedTaxes([19 => -0.16]), new TaxRuleCollection()),
        ];

        yield 'Calculate discount for multiple items' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new CalculatedItem(10, new HighTaxes(), $context),
                new PercentageItem(-10, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-2, -2, new CalculatedTaxes([19 => -0.32]), new TaxRuleCollection()),
        ];

        yield 'Calculate discount for mixed taxes' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new CalculatedItem(10, new LowTaxes(), $context),
                new PercentageItem(-10, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-2, -2, new CalculatedTaxes([19 => -0.16, 7 => -0.07]), new TaxRuleCollection()),
        ];

        yield 'Calculate absolute for mixed taxes' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new CalculatedItem(10, new LowTaxes(), $context),
                new AbsoluteItem(-2, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-2, -2, new CalculatedTaxes([19 => -0.16, 7 => -0.07]), new TaxRuleCollection()),
        ];

        yield 'Calculate discount only for goods' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context, false),
                new CalculatedItem(10, new HighTaxes(), $context, true),
                new AbsoluteItem(-1, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(-1, -1, new CalculatedTaxes([19 => -0.16]), new TaxRuleCollection()),
        ];

        yield 'Calculate surcharge for one item' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new PercentageItem(10, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(1, 1, new CalculatedTaxes([19 => 0.16]), new TaxRuleCollection()),
        ];

        yield 'Calculate surcharge discount' => [
            [
                new CalculatedItem(10, new HighTaxes(), $context),
                new AbsoluteItem(1, self::DISCOUNT_ID),
            ],
            new CalculatedPrice(1, 1, new CalculatedTaxes([19 => 0.16]), new TaxRuleCollection()),
        ];
    }
}
