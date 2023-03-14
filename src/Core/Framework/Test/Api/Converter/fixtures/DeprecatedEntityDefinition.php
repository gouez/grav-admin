<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Api\Converter\fixtures;

use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;

/**
 * @internal
 */
class DeprecatedEntityDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'deprecated_entity';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection();
    }
}
