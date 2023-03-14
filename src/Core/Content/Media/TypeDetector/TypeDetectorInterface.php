<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\TypeDetector;

use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaType\MediaType;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
interface TypeDetectorInterface
{
    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType;
}
