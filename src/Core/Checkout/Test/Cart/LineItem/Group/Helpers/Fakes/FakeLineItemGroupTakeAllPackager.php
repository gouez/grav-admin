<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Cart\LineItem\Group\Helpers\Fakes;

use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroup;
use Laser\Core\Checkout\Cart\LineItem\Group\LineItemGroupPackagerInterface;
use Laser\Core\Checkout\Cart\LineItem\LineItemFlatCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('checkout')]
class FakeLineItemGroupTakeAllPackager implements LineItemGroupPackagerInterface
{
    private int $sequenceCount = 1;

    public function __construct(
        private readonly string $key,
        private readonly FakeSequenceSupervisor $sequenceSupervisor
    ) {
    }

    public function getSequenceCount(): int
    {
        return $this->sequenceCount;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function buildGroupPackage(float $value, LineItemFlatCollection $sortedItems, SalesChannelContext $context): LineItemGroup
    {
        $this->sequenceCount = $this->sequenceSupervisor->getNextCount();

        $group = new LineItemGroup();

        foreach ($sortedItems as $item) {
            $group->addItem($item->getId(), $item->getQuantity());
        }

        return $group;
    }
}
