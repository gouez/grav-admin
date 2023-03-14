<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class CustomerWishlistNotActivatedException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct(
            'Wishlist is not activated!'
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__WISHLIST_IS_NOT_ACTIVATED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
