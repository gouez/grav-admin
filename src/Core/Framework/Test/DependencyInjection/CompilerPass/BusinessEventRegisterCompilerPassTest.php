<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\DependencyInjection\CompilerPass\BusinessEventRegisterCompilerPass;
use Laser\Core\Framework\Event\BusinessEventRegistry;
use Laser\Core\Framework\Framework;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
class BusinessEventRegisterCompilerPassTest extends TestCase
{
    public function testEventsGetAdded(): void
    {
        $container = new ContainerBuilder();
        $container->register(BusinessEventRegistry::class)
            ->setPublic(true);

        $container->addCompilerPass(new BusinessEventRegisterCompilerPass([Framework::class]), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);

        $container->compile(false);
        static::assertSame([Framework::class], $container->get(BusinessEventRegistry::class)->getClasses());
    }
}
