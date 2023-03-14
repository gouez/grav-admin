<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\ContextTokenAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ContextTokenStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof ContextTokenAware || isset($stored[ContextTokenAware::CONTEXT_TOKEN])) {
            return $stored;
        }

        $stored[ContextTokenAware::CONTEXT_TOKEN] = $event->getContextToken();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(ContextTokenAware::CONTEXT_TOKEN)) {
            return;
        }

        $storable->setData(ContextTokenAware::CONTEXT_TOKEN, $storable->getStore(ContextTokenAware::CONTEXT_TOKEN));
    }
}
