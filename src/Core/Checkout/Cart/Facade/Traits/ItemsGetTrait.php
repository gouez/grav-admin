<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade\Traits;

use Laser\Core\Checkout\Cart\Facade\CartFacadeHelper;
use Laser\Core\Checkout\Cart\Facade\ContainerFacade;
use Laser\Core\Checkout\Cart\Facade\ItemFacade;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
trait ItemsGetTrait
{
    private LineItemCollection $items;

    private CartFacadeHelper $helper;

    private SalesChannelContext $context;

    /**
     * `get()` returns the line-item with the given id from this collection.
     *
     * @param string $id The id of the line-item that should be returned.
     *
     * @return ItemFacade|null The line-item with the given id, or null if it does not exist.
     */
    public function get(string $id): ?ItemFacade
    {
        $item = $this->getItems()->get($id);

        if (!$item instanceof LineItem) {
            return null;
        }

        if ($item->getType() === 'container') {
            return new ContainerFacade($item, $this->helper, $this->context);
        }

        return new ItemFacade($item, $this->helper, $this->context);
    }

    private function getItems(): LineItemCollection
    {
        return $this->items;
    }
}
