<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Checkout\Cart\LineItem\CartDataCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
interface CartDataCollectorInterface
{
    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void;
}
