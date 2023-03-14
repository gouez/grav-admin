<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Exception;

use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscount\PromotionDiscountEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class UnknownPromotionDiscountTypeException extends LaserHttpException
{
    public function __construct(PromotionDiscountEntity $discount)
    {
        parent::__construct(
            'Unknown promotion discount type detected: {{ type }}',
            ['type' => $discount->getType()]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__UNKNOWN_PROMOTION_DISCOUNT_TYPE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
