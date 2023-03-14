<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Rule\Rule\Cart;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Checkout\Cart\Rule\GoodsPriceRule;
use Laser\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Laser\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('business-ops')]
class GoodsPriceRuleTest extends TestCase
{
    public function testRuleWithExactPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 270.0, 'operator' => Rule::OPERATOR_EQ]);

        $cart = new Cart('test');
        $cart->add(
            (new LineItem('a', 'a'))
                ->setPrice(new CalculatedPrice(270, 270, new CalculatedTaxCollection(), new TaxRuleCollection()))
        );

        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithExactPriceNotMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 1.0, 'operator' => Rule::OPERATOR_EQ]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualExactPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 270.0, 'operator' => Rule::OPERATOR_LTE]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 300.0, 'operator' => Rule::OPERATOR_LTE]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualPriceNotMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => -1.0, 'operator' => Rule::OPERATOR_LTE]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualExactPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 270.0, 'operator' => Rule::OPERATOR_GTE]);

        $cart = new Cart('test');
        $cart->add(
            (new LineItem('a', 'a'))
                ->setPrice(new CalculatedPrice(270, 270, new CalculatedTaxCollection(), new TaxRuleCollection()))
        );
        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 250.0, 'operator' => Rule::OPERATOR_GTE]);

        $cart = new Cart('test');
        $cart->add(
            (new LineItem('a', 'a'))
                ->setPrice(new CalculatedPrice(270, 270, new CalculatedTaxCollection(), new TaxRuleCollection()))
        );

        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualPriceNotMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 300.0, 'operator' => Rule::OPERATOR_GTE]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithNotEqualPriceMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 200.0, 'operator' => Rule::OPERATOR_NEQ]);

        $cart = new Cart('test');
        $context = $this->createMock(SalesChannelContext::class);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithNotEqualPriceNotMatch(): void
    {
        $rule = (new GoodsPriceRule())->assign(['amount' => 270.0, 'operator' => Rule::OPERATOR_NEQ]);

        $cart = new Cart('test');
        $cart->add(
            (new LineItem('a', 'a'))
                ->setPrice(new CalculatedPrice(270, 270, new CalculatedTaxCollection(), new TaxRuleCollection()))
        );

        $context = $this->createMock(SalesChannelContext::class);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }
}
