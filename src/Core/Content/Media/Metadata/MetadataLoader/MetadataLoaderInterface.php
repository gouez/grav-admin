<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Metadata\MetadataLoader;

use Laser\Core\Content\Media\MediaType\MediaType;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
interface MetadataLoaderInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function extractMetadata(string $filePath): ?array;

    public function supports(MediaType $mediaType): bool;
}
