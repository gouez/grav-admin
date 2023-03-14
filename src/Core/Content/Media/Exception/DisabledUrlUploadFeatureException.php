<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class DisabledUrlUploadFeatureException extends LaserHttpException
{
    public function __construct()
    {
        parent::__construct(
            'The feature to upload a media via URL is disabled.'
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_URL_UPLOAD_DISABLED';
    }
}
