<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Validator\Container;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Test\Cart\Common\FalseRule;
use Laser\Core\Checkout\Test\Cart\Common\TrueRule;
use Laser\Core\Framework\Rule\Container\OrRule;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
class OrRuleTest extends TestCase
{
    public function testTrue(): void
    {
        $rule = new OrRule([
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

    public function testFalse(): void
    {
        $rule = new OrRule([
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
}
