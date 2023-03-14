<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class DeliveryWithoutAddressException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Delivery contains no shipping address');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__DELIVERY_WITHOUT_ADDRESS';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
