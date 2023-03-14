<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class UnknownPaymentMethodException extends LaserHttpException
{
    public function __construct(
        string $paymentMethodId,
        ?\Throwable $e = null
    ) {
        parent::__construct(
            'The payment method {{ paymentMethodId }} could not be found.',
            ['paymentMethodId' => $paymentMethodId],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__UNKNOWN_PAYMENT_METHOD';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
