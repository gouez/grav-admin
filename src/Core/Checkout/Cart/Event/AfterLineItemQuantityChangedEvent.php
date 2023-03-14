<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class AfterLineItemQuantityChangedEvent implements LaserSalesChannelEvent
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var SalesChannelContext
     */
    protected $salesChannelContext;

    public function __construct(
        Cart $cart,
        array $items,
        SalesChannelContext $salesChannelContext
    ) {
        $this->cart = $cart;
        $this->items = $items;
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
