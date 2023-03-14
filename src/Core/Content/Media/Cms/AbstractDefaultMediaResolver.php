<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Cms;

use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
abstract class AbstractDefaultMediaResolver
{
    abstract public function getDecorated(): AbstractDefaultMediaResolver;

    abstract public function getDefaultCmsMediaEntity(string $mediaAssetFilePath): ?MediaEntity;
}
