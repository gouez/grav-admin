<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Cms;

use Laser\Core\Framework\Log\Package;

#[Package('content')]
class VimeoVideoCmsElementResolver extends YoutubeVideoCmsElementResolver
{
    public function getType(): string
    {
        return 'vimeo-video';
    }
}
