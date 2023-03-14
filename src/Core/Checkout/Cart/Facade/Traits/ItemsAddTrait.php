<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade\Traits;

use Laser\Core\Checkout\Cart\Facade\ItemFacade;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
trait ItemsAddTrait
{
    use ItemsGetTrait;

    /**
     * `add()` adds a line-item to this collection.
     *
     * @param ItemFacade $item The line-item that should be added.
     *
     * @return ItemFacade Returns the added line-item.
     *
     * @example add-absolute-discount/add-absolute-discount.twig Add an absolute discount to the cart.
     */
    public function add(ItemFacade $item): ItemFacade
    {
        $this->items->add($item->getItem());

        /** @var ItemFacade $item */
        $item = $this->get($item->getId());

        return $item;
    }

    private function getItems(): LineItemCollection
    {
        return $this->items;
    }
}
