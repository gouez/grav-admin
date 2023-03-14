<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\Event\BusinessEventRegistry;
use Laser\Core\Framework\Test\DependencyInjection\fixtures\TestActionEventCompilerPass;
use Laser\Core\Framework\Test\DependencyInjection\fixtures\TestEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class ActionEventCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();

        $container->register(CustomerDefinition::class, CustomerDefinition::class);
        $container->register(OrderDefinition::class, OrderDefinition::class);

        $container->register(DefinitionInstanceRegistry::class, DefinitionInstanceRegistry::class)
            ->addArgument(new Reference('service_container'))
            ->addArgument([])
            ->addArgument([]);

        $container->register(BusinessEventRegistry::class, BusinessEventRegistry::class)
            ->addArgument(new Reference(DefinitionInstanceRegistry::class));

        $pass = new TestActionEventCompilerPass();
        $pass->process($container);

        $registry = $container->get(BusinessEventRegistry::class);

        $expected = [
            TestEvent::class,
        ];

        static::assertEquals($expected, $registry->getClasses());
    }
}
