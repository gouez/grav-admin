<?php declare(strict_types=1);

namespace Laser\Core\Profiling\Routing;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Routing\RouteScopeWhitelistInterface;
use Laser\Core\Profiling\Controller\ProfilerController;

#[Package('core')]
class ProfilerWhitelist implements RouteScopeWhitelistInterface
{
    public function applies(string $controllerClass): bool
    {
        return $controllerClass === ProfilerController::class;
    }
}
