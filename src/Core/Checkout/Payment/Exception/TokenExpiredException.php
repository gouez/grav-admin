<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class TokenExpiredException extends LaserHttpException
{
    public function __construct(
        string $token,
        ?\Throwable $e = null
    ) {
        parent::__construct(
            'The provided token {{ token }} is expired and the payment could not be processed.',
            ['token' => $token],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__PAYMENT_TOKEN_EXPIRED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_GONE;
    }
}
