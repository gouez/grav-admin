<?php declare(strict_types=1);

namespace Laser\Core\Content\Rule;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Cart\Rule\CartRuleScope;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\RuleAreas;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @extends EntityCollection<RuleEntity>
 */
#[Package('business-ops')]
class RuleCollection extends EntityCollection
{
    /**
     * @deprecated tag:v6.6.0 return type will be native
     *
     * @phpstan-ignore-next-line
     *
     * @return RuleCollection
     */
    public function filterMatchingRules(Cart $cart, SalesChannelContext $context)
    {
        return $this->filter(
            function (RuleEntity $rule) use ($cart, $context) {
                if (!$rule->getPayload() instanceof Rule) {
                    return false;
                }

                return $rule->getPayload()->match(new CartRuleScope($cart, $context));
            }
        );
    }

    public function filterForContext(): self
    {
        return $this->filter(
            fn (RuleEntity $rule) => !$rule->getAreas() || !\in_array(RuleAreas::FLOW_CONDITION_AREA, $rule->getAreas(), true)
        );
    }

    public function filterForFlow(): self
    {
        return $this->filter(
            fn (RuleEntity $rule) => $rule->getAreas() && \in_array(RuleAreas::FLOW_AREA, $rule->getAreas(), true)
        );
    }

    /**
     * @return array<string, string[]>
     */
    public function getIdsByArea(): array
    {
        $idsByArea = [];

        foreach ($this->getElements() as $rule) {
            foreach ($rule->getAreas() ?? [] as $area) {
                $idsByArea[$area] = array_unique(array_merge($idsByArea[$area] ?? [], [$rule->getId()]));
            }
        }

        return $idsByArea;
    }

    public function sortByPriority(): void
    {
        $this->sort(fn (RuleEntity $a, RuleEntity $b) => $b->getPriority() <=> $a->getPriority());
    }

    public function equals(RuleCollection $rules): bool
    {
        if ($this->count() !== $rules->count()) {
            return false;
        }

        foreach ($this->elements as $element) {
            if (!$rules->has($element->getId())) {
                return false;
            }
        }

        return true;
    }

    public function getApiAlias(): string
    {
        return 'rule_collection';
    }

    protected function getExpectedClass(): string
    {
        return RuleEntity::class;
    }
}
