<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class StreamNotReadableException extends LaserHttpException
{
    public function __construct(string $path)
    {
        parent::__construct(
            'Could not read stream at following path: "{{ path }}"',
            ['path' => $path]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_STREAM_NOT_READABLE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
