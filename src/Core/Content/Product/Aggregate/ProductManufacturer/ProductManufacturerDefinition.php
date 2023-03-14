<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductManufacturer;

use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Product\Aggregate\ProductManufacturerTranslation\ProductManufacturerTranslationDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ReverseInherited;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Laser\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductManufacturerDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'product_manufacturer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ProductManufacturerCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductManufacturerEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    public function getHydratorClass(): string
    {
        return ProductManufacturerHydrator::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new StringField('link', 'link'))->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('description'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('products', ProductDefinition::class, 'product_manufacturer_id', 'id'))->addFlags(new SetNullOnDelete(), new ReverseInherited('manufacturer')),
            (new TranslationsAssociationField(ProductManufacturerTranslationDefinition::class, 'product_manufacturer_id'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
