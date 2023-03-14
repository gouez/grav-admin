<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class PaymentMethodNotAvailableException extends LaserHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(
            'The order has no active payment method - {{ id }}',
            ['id' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__UNAVAILABLE_PAYMENT_METHOD';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
