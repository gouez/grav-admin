<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Rule;

use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleConfig;
use Laser\Core\Framework\Rule\RuleConstraints;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class CustomerCreatedByAdminRule extends Rule
{
    final public const RULE_NAME = 'customerCreatedByAdmin';

    /**
     * @internal
     */
    public function __construct(private readonly bool $shouldCustomerBeCreatedByAdmin = true)
    {
        parent::__construct();
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CheckoutRuleScope) {
            return false;
        }

        if (!$customer = $scope->getSalesChannelContext()->getCustomer()) {
            return false;
        }

        return $this->shouldCustomerBeCreatedByAdmin === (bool) $customer->getCreatedById();
    }

    public function getConstraints(): array
    {
        return [
            'shouldCustomerBeCreatedByAdmin' => RuleConstraints::bool(true),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->booleanField('shouldCustomerBeCreatedByAdmin');
    }
}
