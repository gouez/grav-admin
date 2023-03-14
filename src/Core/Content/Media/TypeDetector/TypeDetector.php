<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\TypeDetector;

use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaType\MediaType;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Annotation\Concept\ExtensionPattern\HandlerChain;

/**
 * @HandlerChain(
 *     serviceTag="laser.media_type.detector",
 *     handlerInterface="TypeDetectorInterface"
 * )
 */
#[Package('content')]
class TypeDetector implements TypeDetectorInterface
{
    /**
     * @internal
     *
     * @param TypeDetectorInterface[] $typeDetector
     */
    public function __construct(private readonly iterable $typeDetector)
    {
    }

    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType = null): MediaType
    {
        $mediaType = null;
        foreach ($this->typeDetector as $typeDetector) {
            $mediaType = $typeDetector->detect($mediaFile, $mediaType);
        }

        return $mediaType;
    }
}
