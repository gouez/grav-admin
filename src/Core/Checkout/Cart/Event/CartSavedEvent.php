<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('checkout')]
class CartSavedEvent extends Event implements LaserSalesChannelEvent
{
    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var Cart
     */
    protected $cart;

    public function __construct(
        SalesChannelContext $context,
        Cart $cart
    ) {
        $this->context = $context;
        $this->cart = $cart;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
