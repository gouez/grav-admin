<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('customer-order')]
class InvalidDocumentException extends LaserHttpException
{
    public function __construct(string $documentId)
    {
        $message = sprintf('The document with id "%s" is invalid or could not be found.', $documentId);
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): string
    {
        return 'DOCUMENT__INVALID_DOCUMENT_ID';
    }
}
