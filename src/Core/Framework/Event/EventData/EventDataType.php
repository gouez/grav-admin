<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event\EventData;

use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface EventDataType
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
