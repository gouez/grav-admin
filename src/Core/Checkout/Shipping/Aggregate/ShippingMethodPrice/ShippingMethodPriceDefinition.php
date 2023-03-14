<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Shipping\Aggregate\ShippingMethodPrice;

use Laser\Core\Checkout\Shipping\ShippingMethodDefinition;
use Laser\Core\Content\Rule\RuleDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\PriceField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class ShippingMethodPriceDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'shipping_method_price';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ShippingMethodPriceCollection::class;
    }

    public function getEntityClass(): string
    {
        return ShippingMethodPriceEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): ?string
    {
        return ShippingMethodDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new FkField('shipping_method_id', 'shippingMethodId', ShippingMethodDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new FkField('rule_id', 'ruleId', RuleDefinition::class))->addFlags(new ApiAware()),
            (new IntField('calculation', 'calculation'))->addFlags(new ApiAware()),
            (new FkField('calculation_rule_id', 'calculationRuleId', RuleDefinition::class))->addFlags(new ApiAware()),
            (new FloatField('quantity_start', 'quantityStart'))->addFlags(new ApiAware()),
            (new FloatField('quantity_end', 'quantityEnd'))->addFlags(new ApiAware()),
            (new PriceField('currency_price', 'currencyPrice'))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
            new ManyToOneAssociationField('shippingMethod', 'shipping_method_id', ShippingMethodDefinition::class, 'id', false),
            new ManyToOneAssociationField('rule', 'rule_id', RuleDefinition::class, 'id', false),
            new ManyToOneAssociationField('calculationRule', 'calculation_rule_id', RuleDefinition::class, 'id', false),
        ]);
    }
}
