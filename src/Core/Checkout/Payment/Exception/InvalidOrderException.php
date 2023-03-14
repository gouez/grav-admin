<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class InvalidOrderException extends LaserHttpException
{
    public function __construct(
        string $orderId,
        ?\Throwable $e = null
    ) {
        parent::__construct(
            'The order with id {{ orderId }} is invalid or could not be found.',
            ['orderId' => $orderId],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__INVALID_ORDER_ID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
