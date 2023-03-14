<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade\Traits;

use Laser\Core\Checkout\Cart\Facade\CartFacadeHelper;
use Laser\Core\Checkout\Cart\Facade\ItemFacade;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @implements \IteratorAggregate<array-key, LineItem>
 */
#[Package('checkout')]
trait ItemsIteratorTrait
{
    private CartFacadeHelper $helper;

    private LineItemCollection $items;

    private SalesChannelContext $context;

    /**
     * @internal should not be used directly, loop over an ItemsFacade directly inside twig instead
     */
    public function getIterator(): \ArrayIterator
    {
        $items = [];
        foreach ($this->getItems() as $key => $item) {
            $items[$key] = new ItemFacade($item, $this->helper, $this->context);
        }

        return new \ArrayIterator($items);
    }

    private function getItems(): LineItemCollection
    {
        return $this->items;
    }
}
