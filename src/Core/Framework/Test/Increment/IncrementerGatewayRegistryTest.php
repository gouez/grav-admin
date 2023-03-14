<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Increment;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Increment\AbstractIncrementer;
use Laser\Core\Framework\Increment\Exception\IncrementGatewayNotFoundException;
use Laser\Core\Framework\Increment\IncrementGatewayRegistry;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class IncrementerGatewayRegistryTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGet(): void
    {
        $registry = $this->getContainer()->get('laser.increment.gateway.registry');

        static::assertInstanceOf(AbstractIncrementer::class, $registry->get(IncrementGatewayRegistry::USER_ACTIVITY_POOL));
        static::assertInstanceOf(AbstractIncrementer::class, $registry->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL));
    }

    public function testGetWithInvalidPool(): void
    {
        static::expectException(IncrementGatewayNotFoundException::class);
        static::expectExceptionMessage('Increment gateway for pool "custom_pool" was not found.');

        $registry = $this->getContainer()->get('laser.increment.gateway.registry');
        static::assertNull($registry->get('custom_pool'));
    }
}
