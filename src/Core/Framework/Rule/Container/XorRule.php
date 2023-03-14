<?php declare(strict_types=1);

namespace Laser\Core\Framework\Rule\Container;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\RuleScope;

#[Package('business-ops
XorRule returns true, if exactly one child rule is true')]
class XorRule extends Container
{
    final public const RULE_NAME = 'xorContainer';

    public function match(RuleScope $scope): bool
    {
        $matches = 0;

        foreach ($this->rules as $rule) {
            $match = $rule->match($scope);
            if (!$match) {
                continue;
            }
            ++$matches;
        }

        return $matches === 1;
    }
}
