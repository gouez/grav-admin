<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\LineItem\Group\RulesMatcher;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupDefinition;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
abstract class AbstractAnyRuleLineItemMatcher
{
    abstract public function getDecorated(): AbstractAnyRuleLineItemMatcher;

    abstract public function isMatching(LineItemGroupDefinition $groupDefinition, LineItem $item, SalesChannelContext $context): bool;
}
