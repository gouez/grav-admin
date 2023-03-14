<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Order;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\NestedEvent;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class OrderConvertedEvent extends NestedEvent
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var OrderEntity
     */
    private $order;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Cart
     */
    private $convertedCart;

    public function __construct(
        OrderEntity $order,
        Cart $cart,
        Context $context
    ) {
        $this->context = $context;
        $this->order = $order;
        $this->cart = $cart;
        $this->convertedCart = clone $cart;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getConvertedCart(): Cart
    {
        return $this->convertedCart;
    }

    public function setConvertedCart(Cart $convertedCart): void
    {
        $this->convertedCart = $convertedCart;
    }
}
