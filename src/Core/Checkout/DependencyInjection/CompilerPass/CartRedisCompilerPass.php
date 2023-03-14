<?php declare(strict_types=1);

namespace Laser\Core\Checkout\DependencyInjection\CompilerPass;

use Laser\Core\Checkout\Cart\CartPersister;
use Laser\Core\Checkout\Cart\RedisCartPersister;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class CartRedisCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('laser.cart.redis_url')) {
            $container->removeDefinition('laser.cart.redis');
            $container->removeDefinition(RedisCartPersister::class);

            return;
        }

        $container->removeDefinition(CartPersister::class);
        $container->setAlias(CartPersister::class, RedisCartPersister::class);
    }
}
