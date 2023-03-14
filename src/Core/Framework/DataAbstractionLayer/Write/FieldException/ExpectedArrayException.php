<?php declare(strict_types=1);

namespace Laser\Core\Framework\DataAbstractionLayer\Write\FieldException;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ExpectedArrayException extends LaserHttpException implements WriteFieldException
{
    public function __construct(private readonly string $path)
    {
        parent::__construct('Expected data to be array.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__WRITE_MALFORMED_INPUT';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
