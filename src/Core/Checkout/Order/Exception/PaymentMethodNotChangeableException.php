<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class PaymentMethodNotChangeableException extends LaserHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(
            'The order has an active transaction - {{ id }}',
            ['id' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__PAYMENT_METHOD_UNCHANGEABLE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
