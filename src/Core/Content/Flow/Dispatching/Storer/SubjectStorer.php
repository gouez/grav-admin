<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\SubjectAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class SubjectStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof SubjectAware || isset($stored[SubjectAware::SUBJECT])) {
            return $stored;
        }

        $stored[SubjectAware::SUBJECT] = $event->getSubject();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(SubjectAware::SUBJECT)) {
            return;
        }

        $storable->setData(SubjectAware::SUBJECT, $storable->getStore(SubjectAware::SUBJECT));
    }
}
