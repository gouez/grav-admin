<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel;

use Laser\Core\Framework\DataAbstractionLayer\Facade\RepositoryFacadeHookFactory;
use Laser\Core\Framework\DataAbstractionLayer\Facade\SalesChannelRepositoryFacadeHookFactory;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\SalesChannelContextAware;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\System\SystemConfig\Facade\SystemConfigFacadeHookFactory;

#[Package('core')]
abstract class StoreApiRequestHook extends Hook implements SalesChannelContextAware
{
    /**
     * @return string[]
     */
    public static function getServiceIds(): array
    {
        return [
            RepositoryFacadeHookFactory::class,
            SystemConfigFacadeHookFactory::class,
            SalesChannelRepositoryFacadeHookFactory::class,
        ];
    }
}
