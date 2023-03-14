<?php declare(strict_types=1);

namespace Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient;

use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipientTag\NewsletterRecipientTagDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Language\LanguageDefinition;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;
use Laser\Core\System\Salutation\SalutationDefinition;
use Laser\Core\System\Tag\TagDefinition;

#[Package('customer-order')]
class NewsletterRecipientDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'newsletter_recipient';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return NewsletterRecipientCollection::class;
    }

    public function getEntityClass(): string
    {
        return NewsletterRecipientEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('email', 'email'))->addFlags(new Required()),
            new StringField('title', 'title'),
            new StringField('first_name', 'firstName'),
            new StringField('last_name', 'lastName'),
            new StringField('zip_code', 'zipCode'),
            new StringField('city', 'city'),
            new StringField('street', 'street'),
            (new StringField('status', 'status'))->addFlags(new Required()),
            (new StringField('hash', 'hash'))->addFlags(new Required()),
            new CustomFields(),
            new DateTimeField('confirmed_at', 'confirmedAt'),
            new ManyToManyAssociationField('tags', TagDefinition::class, NewsletterRecipientTagDefinition::class, 'newsletter_recipient_id', 'tag_id'),
            new FkField('salutation_id', 'salutationId', SalutationDefinition::class),
            new ManyToOneAssociationField('salutation', 'salutation_id', SalutationDefinition::class, 'id', false),

            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false),
        ]);
    }
}
