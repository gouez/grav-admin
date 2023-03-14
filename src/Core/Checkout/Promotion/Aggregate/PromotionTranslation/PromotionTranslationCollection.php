<?php
declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Aggregate\PromotionTranslation;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PromotionTranslationEntity>
 */
#[Package('checkout')]
class PromotionTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'promotion_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return PromotionTranslationEntity::class;
    }
}
