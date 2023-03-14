<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Field;

use Laser\Core\Framework\DataAbstractionLayer\Dbal\FieldAccessorBuilder\CustomFieldsAccessorBuilder;
use Laser\Core\Framework\DataAbstractionLayer\FieldSerializer\CustomFieldsSerializer;
use Laser\Core\Framework\Log\Package;

#[Package('core')]
class CustomFields extends JsonField
{
    public function __construct(
        $storageName = 'custom_fields',
        $propertyName = 'customFields'
    ) {
        parent::__construct($storageName, $propertyName);
    }

    public function setPropertyMapping(array $propertyMapping): void
    {
        $this->propertyMapping = $propertyMapping;
    }

    protected function getSerializerClass(): string
    {
        return CustomFieldsSerializer::class;
    }

    protected function getAccessorBuilderClass(): ?string
    {
        return CustomFieldsAccessorBuilder::class;
    }
}
