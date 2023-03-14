<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Rule;

use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Container\ZipCodeRule;
use Laser\Core\Framework\Rule\RuleComparison;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class BillingZipCodeRule extends ZipCodeRule
{
    final public const RULE_NAME = 'customerBillingZipCode';

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CheckoutRuleScope) {
            return false;
        }

        if (!$customer = $scope->getSalesChannelContext()->getCustomer()) {
            return RuleComparison::isNegativeOperator($this->operator);
        }

        if (!$address = $customer->getActiveBillingAddress()) {
            return RuleComparison::isNegativeOperator($this->operator);
        }

        return $this->matchZipCode($address);
    }
}
