<?php declare(strict_types=1);

namespace Laser\Core\Framework\DependencyInjection\CompilerPass;

use Laser\Core\Framework\Adapter\Cache\LaserRedisAdapter;
use Laser\Core\Framework\Adapter\Cache\LaserRedisTagAwareAdapter;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
#[Package('core')]
class RedisPrefixCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $ids = [
            'cache.adapter.redis' => LaserRedisAdapter::class,
            'cache.adapter.redis_tag_aware' => LaserRedisTagAwareAdapter::class,
        ];

        foreach ($ids as $id => $class) {
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $definition = $container->getDefinition($id);
            $definition->setClass($class);
            $definition->addArgument($container->getParameter('laser.cache.redis_prefix'));
        }
    }
}
