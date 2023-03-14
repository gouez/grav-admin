<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Rule;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\CheckoutRuleScope;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('business-ops')]
class CartRuleScope extends CheckoutRuleScope
{
    public function __construct(
        protected Cart $cart,
        SalesChannelContext $context
    ) {
        parent::__construct($context);
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
