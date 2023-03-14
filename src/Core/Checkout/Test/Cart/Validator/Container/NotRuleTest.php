<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Validator\Container;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Test\Cart\Common\FalseRule;
use Laser\Core\Checkout\Test\Cart\Common\TrueRule;
use Laser\Core\Framework\Rule\Container\NotRule;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
class NotRuleTest extends TestCase
{
    public function testTrue(): void
    {
        $rule = new NotRule([
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

    public function testExceptionByMultipleRules(): void
    {
        $this->expectException(\RuntimeException::class);
        new NotRule([
            new FalseRule(),
            new FalseRule(),
            new FalseRule(),
        ]);
    }

    public function testFalse(): void
    {
        $rule = new NotRule([
            new TrueRule(),
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
