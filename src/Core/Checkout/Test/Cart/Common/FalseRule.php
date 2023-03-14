<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Common;

use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleScope;

/**
 * @internal
 */
class FalseRule extends Rule
{
    final public const RULE_NAME = 'false';

    public function match(RuleScope $matchContext): bool
    {
        return false;
    }

    public function getConstraints(): array
    {
        return [];
    }
}
