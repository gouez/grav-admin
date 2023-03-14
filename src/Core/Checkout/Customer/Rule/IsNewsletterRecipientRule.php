<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Rule;

use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Rule;
use Laser\Core\Framework\Rule\RuleConfig;
use Laser\Core\Framework\Rule\RuleConstraints;
use Laser\Core\Framework\Rule\RuleScope;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('business-ops')]
class IsNewsletterRecipientRule extends Rule
{
    final public const RULE_NAME = 'customerIsNewsletterRecipient';

    /**
     * @internal
     */
    public function __construct(protected bool $isNewsletterRecipient = true)
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

        if ($this->isNewsletterRecipient) {
            return $this->matchIsNewsletterRecipient($customer, $scope->getSalesChannelContext());
        }

        return !$this->matchIsNewsletterRecipient($customer, $scope->getSalesChannelContext());
    }

    public function getConstraints(): array
    {
        return [
            'isNewsletterRecipient' => RuleConstraints::bool(true),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->booleanField('isNewsletterRecipient');
    }

    private function matchIsNewsletterRecipient(CustomerEntity $customer, SalesChannelContext $context): bool
    {
        $salesChannelIds = $customer->getNewsletterSalesChannelIds();

        return \is_array($salesChannelIds) && \in_array($context->getSalesChannelId(), $salesChannelIds, true);
    }
}
