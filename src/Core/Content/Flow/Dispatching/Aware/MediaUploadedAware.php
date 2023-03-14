<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
interface MediaUploadedAware extends FlowEventAware
{
    public const MEDIA_ID = 'mediaId';

    public const MEDIA_UPLOADED = 'mediaUploaded';

    public function getMediaId(): string;
}
