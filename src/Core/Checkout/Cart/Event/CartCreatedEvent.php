<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('checkout')]
class CartCreatedEvent extends Event
{
    /**
     * @var Cart
     */
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
