<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\LineItem\Group\RulesMatcher;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupDefinition;
use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupRuleMatcherInterface;
use Laser\Core\Checkout\Cart\LineItem\LineItemFlatCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class AnyRuleMatcher implements LineItemGroupRuleMatcherInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly AbstractAnyRuleLineItemMatcher $anyRuleProvider)
    {
    }

    public function getMatchingItems(
        LineItemGroupDefinition $groupDefinition,
        LineItemFlatCollection $items,
        SalesChannelContext $context
    ): LineItemFlatCollection {
        $matchingItems = [];

        foreach ($items as $item) {
            if ($this->anyRuleProvider->isMatching($groupDefinition, $item, $context)) {
                $matchingItems[] = $item;
            }
        }

        return new LineItemFlatCollection($matchingItems);
    }
}
