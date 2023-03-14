<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class FileNotReadableException extends LaserHttpException
{
    public function __construct(string $path)
    {
        parent::__construct('Import file is not readable at {{ path }}.', ['path' => $path]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_FILE_IS_NOT_READABLE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
