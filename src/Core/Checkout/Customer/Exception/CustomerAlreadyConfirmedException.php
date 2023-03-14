<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class CustomerAlreadyConfirmedException extends LaserHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(
            'The customer with the id "{{ customerId }}" is already confirmed.',
            ['customerId' => $id]
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CUSTOMER_IS_ALREADY_CONFIRMED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_PRECONDITION_FAILED;
    }
}
