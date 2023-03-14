<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class DocumentNumberAlreadyExistsException extends LaserHttpException
{
    public function __construct(?string $number)
    {
        parent::__construct('Document number {{number}} has already been allocated.', [
            'number' => $number,
        ]);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'DOCUMENT__NUMBER_ALREADY_EXISTS';
    }
}
