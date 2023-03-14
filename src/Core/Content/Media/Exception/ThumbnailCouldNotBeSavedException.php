<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class ThumbnailCouldNotBeSavedException extends LaserHttpException
{
    public function __construct(string $url)
    {
        parent::__construct(
            'Thumbnail could not be saved to location: {{ location }}',
            ['location' => $url]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_THUMBNAIL_NOT_SAVED';
    }
}
