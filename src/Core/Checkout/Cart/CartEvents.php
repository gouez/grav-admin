<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class CartEvents
{
    /**
     * @Event("Laser\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent")
     */
    final public const CHECKOUT_ORDER_PLACED = 'checkout.order.placed';
}
