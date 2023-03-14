<?php declare(strict_types=1);

namespace Laser\Core\Framework\Rule\Container;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class OrRule extends Container
{
    final public const RULE_NAME = 'orContainer';

    public function match(RuleScope $scope): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->match($scope)) {
                return true;
            }
        }

        return false;
    }
}
