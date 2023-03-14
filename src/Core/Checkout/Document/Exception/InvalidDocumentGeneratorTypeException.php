<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class InvalidDocumentGeneratorTypeException extends LaserHttpException
{
    public function __construct(string $type)
    {
        $message = sprintf('Unable to find a document generator with type "%s"', $type);
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'DOCUMENT__INVALID_GENERATOR_TYPE';
    }
}
