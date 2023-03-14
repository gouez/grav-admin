<?php declare(strict_types=1);

namespace Laser\Core\System\DeliveryTime\Aggregate\DeliveryTimeTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\DeliveryTime\DeliveryTimeDefinition;

#[Package('customer-order')]
class DeliveryTimeTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'delivery_time_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return DeliveryTimeTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return DeliveryTimeDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
