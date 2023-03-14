<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Rule;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\FlowRule;
use Laser\Core\Framework\Rule\RuleConfig;
use Laser\Core\Framework\Rule\RuleConstraints;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class OrderCreatedByAdminRule extends FlowRule
{
    final public const RULE_NAME = 'orderCreatedByAdmin';

    /**
     * @internal
     */
    public function __construct(private bool $shouldOrderBeCreatedByAdmin = true)
    {
        parent::__construct();
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof FlowRuleScope) {
            return false;
        }

        return $this->shouldOrderBeCreatedByAdmin === (bool) $scope->getOrder()->getCreatedById();
    }

    public function getConstraints(): array
    {
        return [
            'shouldOrderBeCreatedByAdmin' => RuleConstraints::bool(true),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())->booleanField('shouldOrderBeCreatedByAdmin');
    }
}
