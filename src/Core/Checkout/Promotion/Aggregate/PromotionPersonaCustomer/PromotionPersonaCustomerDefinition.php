<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Aggregate\PromotionPersonaCustomer;

use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Promotion\PromotionDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PromotionPersonaCustomerDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'promotion_persona_customer';

    /**
     * This class is used as m:n relation between promotions and customers.
     * It gives the option to assign what customers may use this
     * promotion based on a whitelist algorithm.
     */
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
            (new FkField('promotion_id', 'promotionId', PromotionDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('promotion', 'promotion_id', PromotionDefinition::class, 'id'),
            new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class, 'id'),
        ]);
    }
}
