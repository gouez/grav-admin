<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Metadata;

use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaType\MediaType;
use Laser\Core\Content\Media\Metadata\MetadataLoader\MetadataLoaderInterface;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Annotation\Concept\ExtensionPattern\Handler;

/**
 * @Handler(
 *     servcieTag="laser.metadata.loader",
 *     handlerInterface="MetadataLoaderInterface"
 * )
 */
#[Package('content')]
class MetadataLoader
{
    /**
     * @internal
     *
     * @param MetadataLoaderInterface[] $metadataLoader
     */
    public function __construct(private readonly iterable $metadataLoader)
    {
    }

    public function loadFromFile(MediaFile $mediaFile, MediaType $mediaType): ?array
    {
        foreach ($this->metadataLoader as $loader) {
            if ($loader->supports($mediaType)) {
                $metaData = $loader->extractMetadata($mediaFile->getFileName());

                if ($mediaFile->getHash()) {
                    $metaData['hash'] = $mediaFile->getHash();
                }

                return $metaData;
            }
        }

        return null;
    }
}
