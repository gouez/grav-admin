<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\Rule\Helper;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\Rule\LineItemPropertyRule;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('business-ops')]
class CartRuleScopeCase
{
    /**
     * @param LineItem[] $lineItems
     */
    public function __construct(
        public string $description,
        public bool $match,
        public LineItemPropertyRule $rule,
        public array $lineItems
    ) {
    }
}
