<?php declare(strict_types=1);

namespace Laser\Core\DevOps\StaticAnalyze\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use Laser\Core\Checkout\Cart\Rule\AlwaysValidRule;
use Laser\Core\Checkout\Cart\Rule\GoodsCountRule;
use Laser\Core\Checkout\Cart\Rule\GoodsPriceRule;
use Laser\Core\Checkout\Cart\Rule\LineItemCustomFieldRule;
use Laser\Core\Checkout\Cart\Rule\LineItemGoodsTotalRule;
use Laser\Core\Checkout\Cart\Rule\LineItemGroupRule;
use Laser\Core\Checkout\Cart\Rule\LineItemInCategoryRule;
use Laser\Core\Checkout\Cart\Rule\LineItemPropertyRule;
use Laser\Core\Checkout\Cart\Rule\LineItemPurchasePriceRule;
use Laser\Core\Checkout\Cart\Rule\LineItemRule;
use Laser\Core\Checkout\Cart\Rule\LineItemWithQuantityRule;
use Laser\Core\Checkout\Cart\Rule\LineItemWrapperRule;
use Laser\Core\Checkout\Customer\Rule\BillingZipCodeRule;
use Laser\Core\Checkout\Customer\Rule\CustomerCustomFieldRule;
use Laser\Core\Checkout\Customer\Rule\ShippingZipCodeRule;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\Container\AndRule;
use Laser\Core\Framework\Rule\Container\Container;
use Laser\Core\Framework\Rule\Container\FilterRule;
use Laser\Core\Framework\Rule\Container\MatchAllLineItemsRule;
use Laser\Core\Framework\Rule\Container\NotRule;
use Laser\Core\Framework\Rule\Container\OrRule;
use Laser\Core\Framework\Rule\Container\XorRule;
use Laser\Core\Framework\Rule\Container\ZipCodeRule;
use Laser\Core\Framework\Rule\DateRangeRule;
use Laser\Core\Framework\Rule\Rule as LaserRule;
use Laser\Core\Framework\Rule\ScriptRule;
use Laser\Core\Framework\Rule\SimpleRule;
use Laser\Core\Framework\Rule\TimeRangeRule;

/**
 * @implements Rule<InClassNode>
 *
 * @internal
 */
#[Package('core')]
class RuleConditionHasRuleConfigRule implements Rule
{
    /**
     * @var list<string>
     */
    private array $rulesAllowedToBeWithoutConfig = [
        ZipCodeRule::class,
        FilterRule::class,
        Container::class,
        AndRule::class,
        NotRule::class,
        OrRule::class,
        XorRule::class,
        MatchAllLineItemsRule::class,
        ScriptRule::class,
        DateRangeRule::class,
        SimpleRule::class,
        TimeRangeRule::class,
        GoodsCountRule::class,
        GoodsPriceRule::class,
        LineItemRule::class,
        LineItemWithQuantityRule::class,
        LineItemWrapperRule::class,
        BillingZipCodeRule::class,
        ShippingZipCodeRule::class,
        AlwaysValidRule::class,
        LineItemPropertyRule::class,
        LineItemPurchasePriceRule::class,
        LineItemInCategoryRule::class,
        LineItemCustomFieldRule::class,
        LineItemGoodsTotalRule::class,
        CustomerCustomFieldRule::class,
        LineItemGroupRule::class,
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     *
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->isRuleClass($scope) || $this->isAllowed($scope) || $this->isValid($scope)) {
            if ($this->isAllowed($scope) && $this->isValid($scope)) {
                return ['This class is implementing the getConfig function and has a own admin component. Remove getConfig or the component.'];
            }

            return [];
        }

        return ['This class has to implement getConfig or implement a new admin component.'];
    }

    private function isValid(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null || !$class->hasMethod('getConfig')) {
            return false;
        }

        $declaringClass = $class->getMethod('getConfig', $scope)->getDeclaringClass();

        return $declaringClass->getName() !== LaserRule::class;
    }

    private function isAllowed(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null) {
            return false;
        }

        return \in_array($class->getName(), $this->rulesAllowedToBeWithoutConfig, true);
    }

    private function isRuleClass(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null) {
            return false;
        }

        $namespace = $class->getName();
        if (!\str_contains($namespace, 'Laser\\Tests\\Unit\\') && !\str_contains($namespace, 'Laser\\Tests\\Migration\\')) {
            return false;
        }

        return $class->isSubclassOf(LaserRule::class);
    }
}
