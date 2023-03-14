<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
interface LaserEvent
{
    public function getContext(): Context;
}
