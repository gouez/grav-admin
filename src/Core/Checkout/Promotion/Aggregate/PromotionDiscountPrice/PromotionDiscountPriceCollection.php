<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscountPrice;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PromotionDiscountPriceEntity>
 */
#[Package('checkout')]
class PromotionDiscountPriceCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return PromotionDiscountPriceEntity::class;
    }

    public function getApiAlias(): string
    {
        return 'promotion_discount_price_collection';
    }
}
