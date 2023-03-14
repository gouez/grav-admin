<?php declare(strict_types=1);

namespace Laser\Core\System\SalesChannel\Aggregate\SalesChannelAnalytics;

use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;

#[Package('sales-channel')]
class SalesChannelAnalyticsDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'sales_channel_analytics';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SalesChannelAnalyticsCollection::class;
    }

    public function getEntityClass(): string
    {
        return SalesChannelAnalyticsEntity::class;
    }

    public function getParentDefinitionClass(): ?string
    {
        return SalesChannelDefinition::class;
    }

    public function since(): ?string
    {
        return '6.2.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('tracking_id', 'trackingId'),
            new BoolField('active', 'active'),
            new BoolField('track_orders', 'trackOrders'),
            new BoolField('anonymize_ip', 'anonymizeIp'),
            (new OneToOneAssociationField('salesChannel', 'id', 'analytics_id', SalesChannelDefinition::class, false)),
        ]);
    }
}
