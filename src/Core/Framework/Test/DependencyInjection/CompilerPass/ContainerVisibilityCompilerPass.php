<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DependencyInjection\CompilerPass;

use Laser\Core\Content\Category\Service\NavigationLoader;
use Laser\Core\Content\Product\Cart\ProductLineItemFactory;
use Laser\Core\Content\Seo\HreflangLoaderInterface;
use Laser\Core\Content\Seo\SeoUrlUpdater;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * Marks services public that would otherwise be inlined in setups where only Laser/Core is used,
 * as the only usages are in storefront
 */
class ContainerVisibilityCompilerPass implements CompilerPassInterface
{
    private const PUBLIC_TEST_SERVICES = [
        NavigationLoader::class,
        HreflangLoaderInterface::class,
        ProductLineItemFactory::class,
        SeoUrlUpdater::class,
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::PUBLIC_TEST_SERVICES as $serviceId) {
            $definition = $container->getDefinition($serviceId);
            $definition->setPublic(true);
        }
    }
}
