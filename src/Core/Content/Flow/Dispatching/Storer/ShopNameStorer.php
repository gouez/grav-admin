<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\ShopNameAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ShopNameStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof ShopNameAware || isset($stored[ShopNameAware::SHOP_NAME])) {
            return $stored;
        }

        $stored[ShopNameAware::SHOP_NAME] = $event->getShopName();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(ShopNameAware::SHOP_NAME)) {
            return;
        }

        $storable->setData(ShopNameAware::SHOP_NAME, $storable->getStore(ShopNameAware::SHOP_NAME));
    }
}
