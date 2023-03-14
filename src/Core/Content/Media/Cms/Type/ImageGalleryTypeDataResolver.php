<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Cms\Type;

use Laser\Core\Framework\Log\Package;

#[Package('content')]
class ImageGalleryTypeDataResolver extends ImageSliderTypeDataResolver
{
    public function getType(): string
    {
        return 'image-gallery';
    }
}
