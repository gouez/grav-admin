<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class IllegalUrlException extends LaserHttpException
{
    public function __construct(string $url)
    {
        parent::__construct(
            'Provided URL "{{ url }}" is not allowed.',
            ['url' => $url]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_ILLEGAL_URL';
    }
}
