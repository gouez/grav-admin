<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Rule;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleComparison;
use Laser\Core\Framework\Rule\RuleConfig;
use Laser\Core\Framework\Rule\RuleConstraints;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class CartTotalPurchasePriceRule extends Rule
{
    final public const RULE_NAME = 'cartTotalPurchasePrice';

    protected string $operator = Rule::OPERATOR_EQ;

    protected string $type = 'gross';

    protected float $amount = 0.0;

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CartRuleScope) {
            return false;
        }

        $total = 0.0;

        foreach ($scope->getCart()->getLineItems()->filterGoodsFlat() as $lineItem) {
            $purchasePricePayload = $lineItem->getPayloadValue('purchasePrices');

            if (!$purchasePricePayload) {
                continue;
            }

            $purchasePrice = json_decode((string) $purchasePricePayload, true, 512, \JSON_THROW_ON_ERROR);

            $total += ($purchasePrice[$this->type] ?? 0.0) * $lineItem->getQuantity();
        }

        return RuleComparison::numeric($total, $this->amount, $this->operator);
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::numericOperators(false),
            'type' => RuleConstraints::string(),
            'amount' => RuleConstraints::float(),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_NUMBER)
            ->selectField('type', ['gross', 'net'], false, ['class' => 'is--max-content'])
            ->numberField('amount');
    }
}
