<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\EmailAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class EmailStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof EmailAware || isset($stored[EmailAware::EMAIL])) {
            return $stored;
        }

        $stored[EmailAware::EMAIL] = $event->getEmail();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(EmailAware::EMAIL)) {
            return;
        }

        $storable->setData(EmailAware::EMAIL, $storable->getStore(EmailAware::EMAIL));
    }
}
