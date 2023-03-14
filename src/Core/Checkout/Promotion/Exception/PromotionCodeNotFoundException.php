<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class PromotionCodeNotFoundException extends LaserHttpException
{
    public function __construct(string $code)
    {
        parent::__construct('Promotion Code "{{ code }}" has not been found!', ['code' => $code]);
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CODE_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
