<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\SeoUrl;

use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Language\LanguageDefinition;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;

#[Package('sales-channel')]
class SeoUrlDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'seo_url';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SeoUrlCollection::class;
    }

    public function getEntityClass(): string
    {
        return SeoUrlEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new ApiAware()),
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new IdField('foreign_key', 'foreignKey'))->addFlags(new ApiAware(), new Required()),

            (new StringField('route_name', 'routeName', 50))->addFlags(new ApiAware(), new Required()),
            (new StringField('path_info', 'pathInfo', 750))->addFlags(new ApiAware(), new Required()),
            (new StringField('seo_path_info', 'seoPathInfo', 750))->addFlags(new ApiAware(), new Required()),
            (new BoolField('is_canonical', 'isCanonical'))->addFlags(new ApiAware()),
            (new BoolField('is_modified', 'isModified'))->addFlags(new ApiAware()),
            (new BoolField('is_deleted', 'isDeleted'))->addFlags(new ApiAware()),

            (new StringField('url', 'url'))->addFlags(new ApiAware(), new Runtime()),
            (new CustomFields())->addFlags(new ApiAware()),
            new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false),

            (new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false)),

            (new BoolField('is_valid', 'isValid'))->addFlags(new ApiAware(), new Runtime()),
        ]);
    }
}
