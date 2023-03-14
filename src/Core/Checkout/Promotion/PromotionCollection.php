<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PromotionEntity>
 */
#[Package('checkout')]
class PromotionCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'promotion_collection';
    }

    protected function getExpectedClass(): string
    {
        return PromotionEntity::class;
    }
}
