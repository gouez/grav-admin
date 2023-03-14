<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart;

use Laser\Core\Checkout\Promotion\PromotionEntity;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class PromotionCodeTuple
{
    public function __construct(
        private readonly string $code,
        private readonly PromotionEntity $promotion
    ) {
    }

    /**
     * Gets the code of the tuple.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Gets the promotion for this code tuple.
     */
    public function getPromotion(): PromotionEntity
    {
        return $this->promotion;
    }
}
