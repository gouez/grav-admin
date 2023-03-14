<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\ConfirmUrlAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ConfirmUrlStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof ConfirmUrlAware || isset($stored[ConfirmUrlAware::CONFIRM_URL])) {
            return $stored;
        }

        $stored[ConfirmUrlAware::CONFIRM_URL] = $event->getConfirmUrl();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(ConfirmUrlAware::CONFIRM_URL)) {
            return;
        }

        $storable->setData(ConfirmUrlAware::CONFIRM_URL, $storable->getStore(ConfirmUrlAware::CONFIRM_URL));
    }
}
