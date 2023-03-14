<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Rule;

use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Container\DaysSinceRule;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class DaysSinceLastLoginRule extends DaysSinceRule
{
    final public const RULE_NAME = 'customerDaysSinceLastLogin';

    protected function getDate(RuleScope $scope): ?\DateTimeInterface
    {
        if (!$customer = $scope->getSalesChannelContext()->getCustomer()) {
            return null;
        }

        return $customer->getLastLogin();
    }

    protected function supportsScope(RuleScope $scope): bool
    {
        return $scope instanceof CheckoutRuleScope;
    }
}
