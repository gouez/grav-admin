<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class WrongGuestCredentialsException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Wrong credentials for guest authentication.');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__GUEST_WRONG_CREDENTIALS';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
