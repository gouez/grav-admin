<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
abstract class RefundProcessException extends LaserHttpException
{
    public function __construct(
        private readonly string $refundId,
        string $message,
        array $parameters = [],
        ?\Throwable $e = null
    ) {
        parent::__construct($message, $parameters, $e);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }
}
