<?php declare(strict_types=1);

namespace Laser\Core\Framework\Uuid\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidUuidException extends LaserHttpException
{
    public function __construct(string $uuid)
    {
        parent::__construct('Value is not a valid UUID: {{ input }}', ['input' => $uuid]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_UUID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
