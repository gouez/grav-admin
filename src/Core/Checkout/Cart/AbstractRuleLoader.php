<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Content\Rule\RuleCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('checkout')]
abstract class AbstractRuleLoader
{
    abstract public function getDecorated(): AbstractRuleLoader;

    abstract public function load(Context $context): RuleCollection;
}
