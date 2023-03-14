<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition;

use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityExtension;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('inventory')]
class ProductExtensionSelfReferenced extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new FkField('linked_product_id', 'linkedProductId', ProductDefinition::class)
        );

        $collection->add(
            (new ReferenceVersionField(ProductDefinition::class, 'linked_product_version_id'))->addFlags(new ApiAware(), new Required())
        );

        $collection->add(
            new ManyToOneAssociationField('ManyToOneSelfReference', 'linked_product_id', ProductDefinition::class)
        );

        $collection->add(
            new ManyToOneAssociationField('ManyToOneSelfReferenceAutoload', 'linked_product_id', ProductDefinition::class, 'id', true)
        );

        $collection->add(
            new OneToManyAssociationField('oneToManySelfReferenced', ProductDefinition::class, 'linked_product_id', 'id')
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
