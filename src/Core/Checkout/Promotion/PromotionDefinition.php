<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion;

use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionCartRule\PromotionCartRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscount\PromotionDiscountDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionOrderRule\PromotionOrderRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionPersonaCustomer\PromotionPersonaCustomerDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionPersonaRule\PromotionPersonaRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionSalesChannel\PromotionSalesChannelDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionSetGroup\PromotionSetGroupDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionTranslation\PromotionTranslationDefinition;
use Laser\Core\Content\Rule\RuleDefinition;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ListField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Laser\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PromotionDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'promotion';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return PromotionCollection::class;
    }

    public function getEntityClass(): string
    {
        return PromotionEntity::class;
    }

    /**
     * Gets the default values for new entity instances.
     */
    public function getDefaults(): array
    {
        return [
            'active' => false,
            'exclusive' => false,
            'useCodes' => false,
            'useIndividualCodes' => false,
            'individualCodePattern' => '',
            'useSetGroups' => false,
            'maxRedemptionsGlobal' => null,
            'maxRedemptionsPerCustomer' => null,
            'preventCombination' => false,
            'priority' => 1,
        ];
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new TranslatedField('name'),
            (new BoolField('active', 'active'))->addFlags(new Required()),
            new DateTimeField('valid_from', 'validFrom'),
            new DateTimeField('valid_until', 'validUntil'),
            new IntField('max_redemptions_global', 'maxRedemptionsGlobal'),
            new IntField('max_redemptions_per_customer', 'maxRedemptionsPerCustomer'),
            (new IntField('priority', 'priority'))->addFlags(new Required()),
            (new BoolField('exclusive', 'exclusive'))->addFlags(new Required()),
            new StringField('code', 'code'),
            (new BoolField('use_codes', 'useCodes'))->addFlags(new Required()),
            (new BoolField('use_individual_codes', 'useIndividualCodes'))->addFlags(new Required()),
            new StringField('individual_code_pattern', 'individualCodePattern'),
            (new BoolField('use_setgroups', 'useSetGroups'))->addFlags(new Required()),
            new BoolField('customer_restriction', 'customerRestriction'),
            (new BoolField('prevent_combination', 'preventCombination'))->addFlags(new Required()),

            (new IntField('order_count', 'orderCount'))->addFlags(new WriteProtected(Context::SYSTEM_SCOPE)),
            (new JsonField('orders_per_customer_count', 'ordersPerCustomerCount'))->addFlags(new WriteProtected(Context::SYSTEM_SCOPE)),

            (new OneToManyAssociationField('setgroups', PromotionSetGroupDefinition::class, 'promotion_id'))->addFlags(new CascadeDelete()),

            (new OneToManyAssociationField('salesChannels', PromotionSalesChannelDefinition::class, 'promotion_id', 'id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('discounts', PromotionDiscountDefinition::class, 'promotion_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('individualCodes', PromotionIndividualCodeDefinition::class, 'promotion_id'))->addFlags(new CascadeDelete()),

            (new ManyToManyAssociationField('personaRules', RuleDefinition::class, PromotionPersonaRuleDefinition::class, 'promotion_id', 'rule_id'))->addFlags(new CascadeDelete()),
            (new ManyToManyAssociationField('personaCustomers', CustomerDefinition::class, PromotionPersonaCustomerDefinition::class, 'promotion_id', 'customer_id'))->addFlags(new CascadeDelete()),
            (new ManyToManyAssociationField('orderRules', RuleDefinition::class, PromotionOrderRuleDefinition::class, 'promotion_id', 'rule_id'))->addFlags(new CascadeDelete()),
            (new ManyToManyAssociationField('cartRules', RuleDefinition::class, PromotionCartRuleDefinition::class, 'promotion_id', 'rule_id'))->addFlags(new CascadeDelete()),

            (new OneToManyAssociationField('orderLineItems', OrderLineItemDefinition::class, 'promotion_id'))->addFlags(new SetNullOnDelete()),

            (new TranslationsAssociationField(PromotionTranslationDefinition::class, 'promotion_id'))->addFlags(new Required()),
            new ListField('exclusion_ids', 'exclusionIds', IdField::class),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
        ]);
    }
}
