<?php declare(strict_types=1);

namespace Laser\Core\Framework\DependencyInjection\CompilerPass;

use Laser\Core\Framework\Adapter\Twig\TwigEnvironment;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class TwigEnvironmentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $twigEnvironment = $container->findDefinition('twig');
        // symfony service subscriber somehow don't work, therefore the service has to be public
        $twigEnvironment->setPublic(true);
        $twigEnvironment->setClass(TwigEnvironment::class);
    }
}
