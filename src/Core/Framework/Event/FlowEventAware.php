<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface FlowEventAware extends LaserEvent
{
    public static function getAvailableData(): EventDataCollection;

    public function getName(): string;
}
