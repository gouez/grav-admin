<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document;

use Laser\Core\Checkout\Document\Aggregate\DocumentType\DocumentTypeDefinition;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class DocumentDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'document';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return DocumentCollection::class;
    }

    public function getEntityClass(): string
    {
        return DocumentEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),

            (new FkField('document_type_id', 'documentTypeId', DocumentTypeDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new StringField('file_type', 'fileType'))->addFlags(new ApiAware(), new Required()),
            (new FkField('referenced_document_id', 'referencedDocumentId', self::class))->addFlags(new ApiAware()),

            (new FkField('order_id', 'orderId', OrderDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new FkField('document_media_file_id', 'documentMediaFileId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ReferenceVersionField(OrderDefinition::class, 'order_version_id'))->addFlags(new ApiAware(), new Required()),

            (new JsonField('config', 'config', [], []))->addFlags(new ApiAware(), new Required()),
            (new BoolField('sent', 'sent'))->addFlags(new ApiAware()),
            (new BoolField('static', 'static'))->addFlags(new ApiAware()),
            (new StringField('deep_link_code', 'deepLinkCode'))->addFlags(new ApiAware(), new Required()),
            (new CustomFields())->addFlags(new ApiAware()),

            (new ManyToOneAssociationField('documentType', 'document_type_id', DocumentTypeDefinition::class, 'id'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::ASSOCIATION_SEARCH_RANKING)),
            (new ManyToOneAssociationField('order', 'order_id', OrderDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('referencedDocument', 'referenced_document_id', self::class, 'id', false))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('dependentDocuments', self::class, 'referenced_document_id'))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('documentMediaFile', 'document_media_file_id', MediaDefinition::class, 'id', false))->addFlags(new ApiAware()),
        ]);
    }
}
