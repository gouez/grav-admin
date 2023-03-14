<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Event;

use Laser\Core\Framework\Log\Package;

#[Package('core')]
class AppInstalledEvent extends ManifestChangedEvent
{
    final public const NAME = 'app.installed';

    public function getName(): string
    {
        return self::NAME;
    }
}
