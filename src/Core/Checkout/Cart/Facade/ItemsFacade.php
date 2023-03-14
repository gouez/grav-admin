<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade;

use Laser\Core\Checkout\Cart\Facade\Traits\ItemsAddTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsCountTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsGetTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsHasTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsIteratorTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsRemoveTrait;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @package checkout
 */
/**
 * The ItemsFacade is a wrapper around a collection of line-items.
 *
 * @script-service cart_manipulation
 *
 * @implements \IteratorAggregate<array-key, LineItem>
 */
#[Package('checkout')]
class ItemsFacade implements \IteratorAggregate
{
    use ItemsAddTrait;
    use ItemsHasTrait;
    use ItemsRemoveTrait;
    use ItemsCountTrait;
    use ItemsGetTrait;
    use ItemsIteratorTrait;

    /**
     * @internal
     */
    public function __construct(
        LineItemCollection $items,
        CartFacadeHelper $helper,
        SalesChannelContext $context
    ) {
        $this->items = $items;
        $this->helper = $helper;
        $this->context = $context;
    }

    private function getItems(): LineItemCollection
    {
        return $this->items;
    }
}
