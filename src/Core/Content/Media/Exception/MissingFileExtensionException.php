<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Exception;

use Laser\Core\Framework\Log\Package;

#[Package('content')]
class MissingFileExtensionException extends UploadException
{
    public function __construct()
    {
        parent::__construct('No file extension provided. Please use the "extension" query parameter to specify the extension of the uploaded file');
    }
}
