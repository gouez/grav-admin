<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Validator;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Test\Cart\Common\FalseRule;
use Laser\Core\Checkout\Test\Cart\Common\TrueRule;
use Laser\Core\Framework\Rule\Container\AndRule;
use Laser\Core\Framework\Rule\Container\OrRule;
use Laser\Core\Framework\Rule\RuleCollection;

/**
 * @internal
 */
class RuleCollectionTest extends TestCase
{
    public function testMetaCollecting(): void
    {
        $collection = new RuleCollection([
            new TrueRule(),
            new AndRule([
                new TrueRule(),
                new OrRule([
                    new TrueRule(),
                    new FalseRule(),
                ]),
            ]),
        ]);

        static::assertTrue($collection->has(FalseRule::class));
        static::assertTrue($collection->has(OrRule::class));
        static::assertEquals(
            new RuleCollection([
                new FalseRule(),
            ]),
            $collection->filterInstance(FalseRule::class)
        );
    }
}
