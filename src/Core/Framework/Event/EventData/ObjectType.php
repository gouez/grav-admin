<?php declare(strict_types=1);

namespace Laser\Core\Framework\Event\EventData;

use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ObjectType implements EventDataType
{
    final public const TYPE = 'object';

    private ?array $data = null;

    public function add(string $name, EventDataType $type): self
    {
        $this->data[$name] = $type->toArray();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => self::TYPE,
            'data' => $this->data,
        ];
    }
}
