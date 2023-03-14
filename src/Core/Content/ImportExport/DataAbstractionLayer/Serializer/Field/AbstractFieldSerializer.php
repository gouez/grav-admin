<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Field;

use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\Entity\AbstractEntitySerializer;
use Laser\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Laser\Core\Content\ImportExport\Struct\Config;
use Laser\Core\Framework\DataAbstractionLayer\Field\Field;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
abstract class AbstractFieldSerializer
{
    protected SerializerRegistry $serializerRegistry;

    abstract public function serialize(Config $config, Field $field, $value): iterable;

    abstract public function deserialize(Config $config, Field $field, $value);

    abstract public function supports(Field $field): bool;

    public function setRegistry(SerializerRegistry $serializerRegistry): void
    {
        $this->serializerRegistry = $serializerRegistry;
    }

    protected function getDecorated(): AbstractEntitySerializer
    {
        throw new \RuntimeException('Implement getDecorated');
    }
}
