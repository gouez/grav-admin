<?php declare(strict_types=1);

namespace Laser\Core\Framework\App\Event\Hooks;

use Laser\Core\Framework\DataAbstractionLayer\Facade\RepositoryFacadeHookFactory;
use Laser\Core\Framework\DataAbstractionLayer\Facade\RepositoryWriterFacadeHookFactory;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\System\SystemConfig\Facade\SystemConfigFacadeHookFactory;

/**
 * @internal
 */
#[Package('core')]
abstract class AppLifecycleHook extends Hook
{
    public static function getServiceIds(): array
    {
        return [
            RepositoryFacadeHookFactory::class,
            SystemConfigFacadeHookFactory::class,
            RepositoryWriterFacadeHookFactory::class,
        ];
    }
}
