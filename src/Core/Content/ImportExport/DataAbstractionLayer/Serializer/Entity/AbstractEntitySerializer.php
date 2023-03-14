<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity;

use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('core')]
abstract class AbstractEntitySerializer
{
    protected SerializerRegistry $serializerRegistry;

    /**
     * @param array<mixed>|Struct|null $entity
     *
     * @return \Generator
     */
    abstract public function serialize(Config $config, EntityDefinition $definition, $entity): iterable;

    /**
     * @param array<mixed>|\Traversable<mixed> $entity
     *
     * @return array<mixed>|\Traversable<mixed>
     */
    abstract public function deserialize(Config $config, EntityDefinition $definition, $entity);

    abstract public function supports(string $entity): bool;

    public function setRegistry(SerializerRegistry $serializerRegistry): void
    {
        $this->serializerRegistry = $serializerRegistry;
    }

    protected function getDecorated(): AbstractEntitySerializer
    {
        throw new \RuntimeException('Implement getDecorated');
    }
}
