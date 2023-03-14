<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Pathname\PathnameStrategy;

use Laser\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class PlainPathnameStrategy extends AbstractPathNameStrategy
{
    /**
     * {@inheritdoc}
     */
    public function generatePathHash(MediaEntity $media, ?MediaThumbnailEntity $thumbnail = null): ?string
    {
        return null;
    }

    /**
     * Name of the strategy
     */
    public function getName(): string
    {
        return 'plain';
    }
}
