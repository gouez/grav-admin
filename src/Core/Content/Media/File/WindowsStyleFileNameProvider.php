<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\File;

use Laser\Core\Content\Media\MediaCollection;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class WindowsStyleFileNameProvider extends FileNameProvider
{
    protected function getNextFileName(string $originalFileName, MediaCollection $relatedMedia, int $iteration): string
    {
        $suffix = $iteration === 0 ? '' : "_($iteration)";

        return $originalFileName . $suffix;
    }
}
