<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class CustomerNotFoundByHashException extends LaserHttpException
{
    public function __construct(string $hash)
    {
        parent::__construct(
            'No matching customer for the hash "{{ hash }}" was found.',
            ['hash' => $hash]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CUSTOMER_NOT_FOUND_BY_HASH';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
