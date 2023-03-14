<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\LineItem\Group\Packager;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroup;
use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupPackagerInterface;
use Laser\Core\Checkout\Cart\LineItem\LineItemFlatCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class LineItemGroupCountPackager implements LineItemGroupPackagerInterface
{
    public function getKey(): string
    {
        return 'COUNT';
    }

    /**
     * This packager builds a bundle for the first x items
     * until the maximum number of items in the group is reached.
     * If not enough items are found to fill the group, then an empty list will be returned.
     */
    public function buildGroupPackage(float $maxItemsInGroup, LineItemFlatCollection $sortedItems, SalesChannelContext $context): LineItemGroup
    {
        $matchedCount = 0;
        $result = new LineItemGroup();

        foreach ($sortedItems as $lineItem) {
            $quantity = $lineItem->getQuantity();

            // add the item to our result
            // with the current quantity
            $result->addItem($lineItem->getId(), $quantity);

            $matchedCount += $quantity;

            // as long as we have not filled our maximum count
            // add all items that match our group rules
            if ($matchedCount >= $maxItemsInGroup) {
                break;
            }
        }

        // if we have less results than our max items
        // return an empty list, because that is not a valid group
        if ($matchedCount < $maxItemsInGroup) {
            return new LineItemGroup();
        }

        return $result;
    }
}
