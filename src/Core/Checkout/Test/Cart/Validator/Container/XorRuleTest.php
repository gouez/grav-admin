<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Validator\Container;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Test\Cart\Common\FalseRule;
use Laser\Core\Checkout\Test\Cart\Common\TrueRule;
use Laser\Core\Framework\Rule\Container\XorRule;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
class XorRuleTest extends TestCase
{
    public function testSingleTrueRule(): void
    {
        $rule = new XorRule([
            new FalseRule(),
            new TrueRule(),
            new FalseRule(),
        ]);

        static::assertTrue(
            $rule->match(
                new CheckoutRuleScope(
                    $this->createMock(SalesChannelContext::class)
                )
            )
        );
    }

    public function testWithMultipleFalse(): void
    {
        $rule = new XorRule([
            new FalseRule(),
            new FalseRule(),
        ]);

        static::assertFalse(
            $rule->match(
                new CheckoutRuleScope(
                    $this->createMock(SalesChannelContext::class)
                )
            )
        );
    }

    public function testWithMultipleTrue(): void
    {
        $rule = new XorRule([
            new TrueRule(),
            new TrueRule(),
            new FalseRule(),
        ]);

        static::assertFalse(
            $rule->match(
                new CheckoutRuleScope(
                    $this->createMock(SalesChannelContext::class)
                )
            )
        );
    }
}
