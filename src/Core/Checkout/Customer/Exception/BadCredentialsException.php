<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class BadCredentialsException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct('Invalid username and/or password.');
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CUSTOMER_AUTH_BAD_CREDENTIALS';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
