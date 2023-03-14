<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\ResetUrlAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ResetUrlStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof ResetUrlAware || isset($stored[ResetUrlAware::RESET_URL])) {
            return $stored;
        }

        $stored[ResetUrlAware::RESET_URL] = $event->getResetUrl();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(ResetUrlAware::RESET_URL)) {
            return;
        }

        $storable->setData(ResetUrlAware::RESET_URL, $storable->getStore(ResetUrlAware::RESET_URL));
    }
}
