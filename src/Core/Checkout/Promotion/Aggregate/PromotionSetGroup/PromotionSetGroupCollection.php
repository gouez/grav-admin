<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Aggregate\PromotionSetGroup;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PromotionSetGroupEntity>
 */
#[Package('checkout')]
class PromotionSetGroupCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'promotion_set_group_collection';
    }

    protected function getExpectedClass(): string
    {
        return PromotionSetGroupEntity::class;
    }
}
