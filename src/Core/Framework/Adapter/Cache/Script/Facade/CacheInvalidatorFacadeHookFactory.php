<?php declare(strict_types=1);

namespace Laser\Core\Framework\Adapter\Cache\Script\Facade;

use Laser\Core\Framework\Adapter\Cache\CacheInvalidator;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Laser\Core\Framework\Script\Execution\Hook;
use Laser\Core\Framework\Script\Execution\Script;

/**
 * @internal
 */
#[Package('core')]
class CacheInvalidatorFacadeHookFactory extends HookServiceFactory
{
    public function __construct(private readonly CacheInvalidator $cacheInvalidator)
    {
    }

    public function factory(Hook $hook, Script $script): CacheInvalidatorFacade
    {
        return new CacheInvalidatorFacade($this->cacheInvalidator);
    }

    public function getName(): string
    {
        return 'cache';
    }
}
