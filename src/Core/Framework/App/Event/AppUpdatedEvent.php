<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Event;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class AppUpdatedEvent extends ManifestChangedEvent
{
    final public const NAME = 'app.updated';

    public function getName(): string
    {
        return self::NAME;
    }
}
