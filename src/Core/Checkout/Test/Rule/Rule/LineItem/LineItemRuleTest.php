<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Rule\Rule\LineItem;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Checkout\Cart\Rule\LineItemRule;
use Laser\Core\Checkout\Cart\Rule\LineItemScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('business-ops')]
class LineItemRuleTest extends TestCase
{
    public function testRuleMatch(): void
    {
        $rule = (new LineItemRule())
            ->assign(['identifiers' => ['A']]);

        $context = $this->createMock(SalesChannelContext::class);

        $lineItem = new LineItem('A', 'product', 'A');

        static::assertTrue(
            $rule->match(new LineItemScope($lineItem, $context))
        );

        $cart = new Cart('test');
        $cart->add($lineItem);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleNotMatch(): void
    {
        $rule = (new LineItemRule())
            ->assign(['identifiers' => ['A']]);

        $context = $this->createMock(SalesChannelContext::class);

        $lineItem = new LineItem('A', 'product', 'B');

        static::assertFalse(
            $rule->match(new LineItemScope($lineItem, $context))
        );

        $cart = new Cart('test');
        $cart->add($lineItem);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }
}
