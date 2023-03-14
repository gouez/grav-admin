<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;

#[Package('content')]
class DuplicatedMediaFileNameException extends LaserHttpException
{
    public function __construct(
        string $fileName,
        string $fileExtension
    ) {
        parent::__construct(
            'A file with the name "{{ fileName }}.{{ fileExtension }}" already exists.',
            ['fileName' => $fileName, 'fileExtension' => $fileExtension]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_DUPLICATED_FILE_NAME';
    }
}
