<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer;

use Laser\Core\Framework\DataAbstractionLayer\Exception\MappingEntityClassesException;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
abstract class MappingEntityDefinition extends EntityDefinition
{
    public function getCollectionClass(): string
    {
        throw new MappingEntityClassesException();
    }

    public function getEntityClass(): string
    {
        throw new MappingEntityClassesException();
    }

    protected function getBaseFields(): array
    {
        return [];
    }

    protected function defaultFields(): array
    {
        return [];
    }
}
