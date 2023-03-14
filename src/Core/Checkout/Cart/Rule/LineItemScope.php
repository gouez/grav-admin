<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Rule;

use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('business-ops')]
class LineItemScope extends CheckoutRuleScope
{
    public function __construct(
        protected LineItem $lineItem,
        SalesChannelContext $context
    ) {
        parent::__construct($context);
    }

    public function getLineItem(): LineItem
    {
        return $this->lineItem;
    }
}
