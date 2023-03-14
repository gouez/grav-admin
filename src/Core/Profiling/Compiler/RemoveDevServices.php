<?php declare(strict_types=1);

namespace Laser\Core\Profiling\Compiler;

use Composer\InstalledVersions;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Profiling\Controller\ProfilerController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @codeCoverageIgnore It's not possible to test without hacky solutions and relying on internals
 */
#[Package('core')]
class RemoveDevServices implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!InstalledVersions::isInstalled('symfony/web-profiler-bundle')) {
            $container->removeDefinition(ProfilerController::class);
        }
    }
}
