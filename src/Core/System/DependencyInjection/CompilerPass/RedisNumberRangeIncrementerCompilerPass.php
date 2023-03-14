<?php declare(strict_types=1);

namespace Laser\Core\System\DependencyInjection\CompilerPass;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\NumberRange\ValueGenerator\Pattern\IncrementStorage\IncrementRedisStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class RedisNumberRangeIncrementerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('laser.number_range.redis_url')) {
            $container->removeDefinition('laser.number_range.redis');
            $container->removeDefinition(IncrementRedisStorage::class);
        }
    }
}
