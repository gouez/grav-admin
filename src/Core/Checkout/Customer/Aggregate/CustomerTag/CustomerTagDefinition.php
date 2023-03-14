<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Aggregate\CustomerTag;

use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Tag\TagDefinition;

#[Package('customer-order')]
class CustomerTagDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'customer_tag';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new FkField('tag_id', 'tagId', TagDefinition::class))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class, 'id', false),
            (new ManyToOneAssociationField('tag', 'tag_id', TagDefinition::class, 'id', false))->addFlags(new ApiAware()),
        ]);
    }
}
