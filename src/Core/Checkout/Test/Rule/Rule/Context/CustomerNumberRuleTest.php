<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Rule\Rule\Context;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\Rule\CustomerNumberRule;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('business-ops')]
class CustomerNumberRuleTest extends TestCase
{
    public function testExactMatch(): void
    {
        $rule = (new CustomerNumberRule())->assign(['numbers' => ['NO. 1']]);

        $cart = new Cart('test');

        $customer = new CustomerEntity();
        $customer->setCustomerNumber('NO. 1');

        $context = $this->createMock(SalesChannelContext::class);

        $context
            ->method('getCustomer')
            ->willReturn($customer);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testMultipleNumbers(): void
    {
        $rule = (new CustomerNumberRule())->assign(['numbers' => ['NO. 1', 'NO. 2', 'NO. 3']]);

        $cart = new Cart('test');

        $customer = new CustomerEntity();
        $customer->setCustomerNumber('NO. 2');

        $context = $this->createMock(SalesChannelContext::class);

        $context
            ->method('getCustomer')
            ->willReturn($customer);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testCaseInsensitive(): void
    {
        $rule = (new CustomerNumberRule())->assign(['numbers' => ['NO. 1']]);

        $cart = new Cart('test');

        $customer = new CustomerEntity();
        $customer->setCustomerNumber('no. 1');

        $context = $this->createMock(SalesChannelContext::class);

        $context
            ->method('getCustomer')
            ->willReturn($customer);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testWithoutCustomer(): void
    {
        $rule = (new CustomerNumberRule())->assign(['numbers' => ['NO. 1']]);

        $cart = new Cart('test');

        $context = $this->createMock(SalesChannelContext::class);

        $context
            ->method('getCustomer')
            ->willReturn(null);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testNotMatch(): void
    {
        $rule = (new CustomerNumberRule())->assign(['numbers' => ['NO. 1']]);

        $cart = new Cart('test');

        $customer = new CustomerEntity();
        $customer->setCustomerNumber('no. 2');

        $context = $this->createMock(SalesChannelContext::class);

        $context
            ->method('getCustomer')
            ->willReturn($customer);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }
}
