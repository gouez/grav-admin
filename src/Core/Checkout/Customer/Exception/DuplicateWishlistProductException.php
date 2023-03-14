<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class DuplicateWishlistProductException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Product already added in wishlist');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__DUPLICATE_WISHLIST_PRODUCT';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
