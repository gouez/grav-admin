<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserSalesChannelEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('checkout')]
class CartVerifyPersistEvent extends Event implements LaserSalesChannelEvent
{
    public function __construct(
        protected SalesChannelContext $context,
        protected Cart $cart,
        protected bool $shouldPersist
    ) {
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

    public function shouldBePersisted(): bool
    {
        return $this->shouldPersist;
    }

    public function setShouldPersist(bool $persist): void
    {
        $this->shouldPersist = $persist;
    }
}
