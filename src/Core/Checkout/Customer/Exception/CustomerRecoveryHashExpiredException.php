<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class CustomerRecoveryHashExpiredException extends LaserHttpException
{
    public function __construct(string $hash)
    {
        parent::__construct(
            'The hash "{{ hash }}" is expired.',
            ['hash' => $hash]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CUSTOMER_RECOVERY_HASH_EXPIRED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_GONE;
    }
}
