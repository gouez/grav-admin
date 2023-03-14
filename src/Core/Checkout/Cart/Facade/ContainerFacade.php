<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Facade;

use Laser\Core\Checkout\Cart\Facade\Traits\DiscountTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsCountTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsGetTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsHasTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsIteratorTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\ItemsRemoveTrait;
use Laser\Core\Checkout\Cart\Facade\Traits\SurchargeTrait;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\LineItem\LineItemCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @package checkout
 */
/**
 * The ContainerFacade allows you to wrap multiple line-items inside a container line-item.
 *
 * @script-service cart_manipulation
 *
 * @internal
 */
#[Package('checkout')]
class ContainerFacade extends ItemFacade
{
    use DiscountTrait;
    use SurchargeTrait;
    use ItemsGetTrait;
    use ItemsRemoveTrait;
    use ItemsHasTrait;
    use ItemsCountTrait;
    use ItemsIteratorTrait;

    private readonly LineItem $item;

    /**
     * @internal
     */
    public function __construct(
        LineItem $item,
        CartFacadeHelper $helper,
        SalesChannelContext $context
    ) {
        parent::__construct($item, $helper, $context);

        $this->item = $item;
        $this->helper = $helper;
        $this->context = $context;
    }

    /**
     * The `product()` method returns all products inside the current container for further manipulation.
     * Similar to the `children()` method, but the line-items are filtered, to only contain product line items.
     *
     * @return ProductsFacade A `ProductsFacade` containing all product line-items inside the current container as a collection.
     */
    public function products(): ProductsFacade
    {
        return new ProductsFacade($this->item->getChildren(), $this->helper, $this->context);
    }

    /**
     * Use the `add()` method to add an item to this container.
     *
     * @param ItemFacade $item The item that should be added.
     *
     * @return ItemFacade The item that was added to the container.
     *
     * @example add-container/add-container.twig 12 1 Add a product to the container and reduce the quantity of the original line-item.
     */
    public function add(ItemFacade $item): ItemFacade
    {
        $this->item->getChildren()->add($item->getItem());

        /** @var ItemFacade $item */
        $item = $this->get($item->getId());

        return $item;
    }

    protected function getItems(): LineItemCollection
    {
        // switch items pointer to children. Used for Items*Traits and DiscountTrait
        return $this->item->getChildren();
    }
}
