<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class EmptyCartException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Cart is empty');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CART_EMPTY';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
