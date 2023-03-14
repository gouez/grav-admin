<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Event;

use Laser\Core\Checkout\Cart\Cart;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('checkout')]
class CartBeforeSerializationEvent extends Event
{
    /**
     * @param array<mixed> $customFieldAllowList
     */
    public function __construct(
        protected Cart $cart,
        private array $customFieldAllowList
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @return array<mixed>
     */
    public function getCustomFieldAllowList(): array
    {
        return $this->customFieldAllowList;
    }

    /**
     * @param array<mixed> $customFieldAllowList
     */
    public function setCustomFieldAllowList(array $customFieldAllowList): void
    {
        $this->customFieldAllowList = $customFieldAllowList;
    }
}
