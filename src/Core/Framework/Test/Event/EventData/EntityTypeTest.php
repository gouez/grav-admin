<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Event\EventData;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Framework\Event\EventData\EntityType;

/**
 * @internal
 */
class EntityTypeTest extends TestCase
{
    public function testToArray(): void
    {
        $definition = CustomerDefinition::class;

        $expected = [
            'type' => 'entity',
            'entityClass' => CustomerDefinition::class,
        ];

        static::assertEquals($expected, (new EntityType($definition))->toArray());
        static::assertEquals($expected, (new EntityType(new CustomerDefinition()))->toArray());
    }
}
