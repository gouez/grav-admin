<?php declare(strict_types=1);

namespace Laser\Core\Content\MailTemplate\Aggregate\MailHeaderFooterTranslation;

use Laser\Core\Content\MailTemplate\Aggregate\MailHeaderFooter\MailHeaderFooterDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('sales-channel')]
class MailHeaderFooterTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'mail_header_footer_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return MailHeaderFooterTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return MailHeaderFooterTranslationCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return MailHeaderFooterDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('description', 'description'))->addFlags(new ApiAware()),
            (new LongTextField('header_html', 'headerHtml'))->addFlags(new ApiAware(), new AllowHtml()),
            (new LongTextField('header_plain', 'headerPlain'))->addFlags(new ApiAware()),
            (new LongTextField('footer_html', 'footerHtml'))->addFlags(new ApiAware(), new AllowHtml()),
            (new LongTextField('footer_plain', 'footerPlain'))->addFlags(new ApiAware()),
        ]);
    }
}
