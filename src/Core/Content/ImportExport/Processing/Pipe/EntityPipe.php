<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Processing\Pipe;

use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\AbstractEntitySerializer;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\PrimaryKeyResolver;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('system-settings')]
class EntityPipe extends AbstractPipe
{
    public function __construct(
        private readonly DefinitionInstanceRegistry $definitionInstanceRegistry,
        private readonly SerializerRegistry $serializerRegistry,
        private ?EntityDefinition $definition = null,
        private ?AbstractEntitySerializer $entitySerializer = null,
        private readonly ?PrimaryKeyResolver $primaryKeyResolver = null
    ) {
    }

    /**
     * @param iterable|Struct $record
     */
    public function in(Config $config, $record): iterable
    {
        $this->loadConfig($config);

        return $this->entitySerializer->serialize($config, $this->definition, $record);
    }

    public function out(Config $config, iterable $record): iterable
    {
        $this->loadConfig($config);

        if ($this->primaryKeyResolver) {
            $record = $this->primaryKeyResolver->resolvePrimaryKeyFromUpdatedBy($config, $this->definition, $record);
        }

        return $this->entitySerializer->deserialize($config, $this->definition, $record);
    }

    private function loadConfig(Config $config): void
    {
        $this->definition ??= $this->definitionInstanceRegistry->getByEntityName($config->get('sourceEntity') ?? '');

        $this->entitySerializer ??= $this->serializerRegistry->getEntity($this->definition->getEntityName());
    }
}
