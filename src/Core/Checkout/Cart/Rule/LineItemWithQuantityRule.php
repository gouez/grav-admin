<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Rule;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Exception\UnsupportedOperatorException;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleComparison;
use Laser\Core\Framework\Rule\RuleConstraints;
use Laser\Core\Framework\Rule\RuleScope;
use Laser\Core\Framework\Validation\Constraint\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Package('business-ops')]
class LineItemWithQuantityRule extends Rule
{
    final public const RULE_NAME = 'cartLineItemWithQuantity';

    /**
     * @internal
     */
    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?string $id = null,
        protected ?int $quantity = null
    ) {
        parent::__construct();
    }

    /**
     * @throws UnsupportedOperatorException
     */
    public function match(RuleScope $scope): bool
    {
        if ($scope instanceof LineItemScope) {
            return $this->lineItemMatches($scope->getLineItem());
        }

        if (!$scope instanceof CartRuleScope) {
            return false;
        }

        foreach ($scope->getCart()->getLineItems()->filterGoodsFlat() as $lineItem) {
            if ($this->lineItemMatches($lineItem)) {
                return true;
            }
        }

        return false;
    }

    public function getConstraints(): array
    {
        return [
            'id' => [new NotBlank(), new Uuid()],
            'quantity' => RuleConstraints::int(),
            'operator' => RuleConstraints::numericOperators(false),
        ];
    }

    private function lineItemMatches(LineItem $lineItem): bool
    {
        if ($lineItem->getReferencedId() !== $this->id && $lineItem->getPayloadValue('parentId') !== $this->id) {
            return false;
        }

        if ($this->quantity === null) {
            return true;
        }

        return RuleComparison::numeric($lineItem->getQuantity(), $this->quantity, $this->operator);
    }
}
