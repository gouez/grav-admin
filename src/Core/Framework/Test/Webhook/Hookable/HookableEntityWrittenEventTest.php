<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Webhook\Hookable;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Webhook\AclPrivilegeCollection;
use Laser\Core\Framework\Webhook\Hookable\HookableEntityWrittenEvent;

/**
 * @internal
 */
class HookableEntityWrittenEventTest extends TestCase
{
    public function testGetter(): void
    {
        $entityId = Uuid::randomHex();
        $event = HookableEntityWrittenEvent::fromWrittenEvent($this->getEntityWrittenEvent($entityId));

        static::assertEquals('product.written', $event->getName());
        static::assertEquals([
            [
                'entity' => 'product',
                'operation' => 'delete',
                'primaryKey' => $entityId,
                'updatedFields' => [],
            ],
        ], $event->getWebhookPayload());
    }

    public function testIsAllowed(): void
    {
        $entityId = Uuid::randomHex();
        $event = HookableEntityWrittenEvent::fromWrittenEvent($this->getEntityWrittenEvent($entityId));

        $allowedPermissions = new AclPrivilegeCollection([
            ProductDefinition::ENTITY_NAME . ':' . AclRoleDefinition::PRIVILEGE_READ,
        ]);
        static::assertTrue($event->isAllowed(
            Uuid::randomHex(),
            $allowedPermissions
        ));

        $notAllowedPermissions = new AclPrivilegeCollection([
            CustomerDefinition::ENTITY_NAME . ':' . AclRoleDefinition::PRIVILEGE_READ,
        ]);
        static::assertFalse($event->isAllowed(
            Uuid::randomHex(),
            $notAllowedPermissions
        ));
    }

    private function getEntityWrittenEvent(string $entityId): EntityWrittenEvent
    {
        $context = Context::createDefaultContext();

        return new EntityWrittenEvent(
            ProductDefinition::ENTITY_NAME,
            [
                new EntityWriteResult(
                    $entityId,
                    [],
                    ProductDefinition::ENTITY_NAME,
                    EntityWriteResult::OPERATION_DELETE,
                    null,
                    null
                ),
            ],
            $context
        );
    }
}
