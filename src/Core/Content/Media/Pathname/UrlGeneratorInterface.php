<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Pathname;

use Laser\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
interface UrlGeneratorInterface
{
    public function getAbsoluteMediaUrl(MediaEntity $media): string;

    public function getRelativeMediaUrl(MediaEntity $media): string;

    public function getAbsoluteThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string;

    public function getRelativeThumbnailUrl(MediaEntity $media, MediaThumbnailEntity $thumbnail): string;
}
