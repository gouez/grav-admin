<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\File;

use Laser\Core\Content\Media\MediaCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
abstract class FileNameProvider
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $mediaRepository)
    {
    }

    public function provide(
        string $preferredFileName,
        string $fileExtension,
        ?string $mediaId,
        Context $context
    ): string {
        $mediaWithRelatedFilename = $this->finderOtherMediaWithFileName(
            $preferredFileName,
            $fileExtension,
            $mediaId,
            $context
        );

        return $this->getPossibleFileName($mediaWithRelatedFilename, $preferredFileName);
    }

    abstract protected function getNextFileName(
        string $originalFileName,
        MediaCollection $relatedMedia,
        int $iteration
    ): string;

    private function finderOtherMediaWithFileName(
        string $fileName,
        string $fileExtension,
        ?string $mediaId,
        Context $context
    ): MediaCollection {
        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(
            MultiFilter::CONNECTION_AND,
            [
                new ContainsFilter('fileName', $fileName),
                new EqualsFilter('fileExtension', $fileExtension),
                new NotFilter(NotFilter::CONNECTION_AND, [new EqualsFilter('id', $mediaId)]),
            ]
        ));

        $search = $this->mediaRepository->search($criteria, $context);

        /** @var MediaCollection $mediaCollection */
        $mediaCollection = $search->getEntities();

        return $mediaCollection;
    }

    private function getPossibleFileName(
        MediaCollection $relatedMedia,
        string $preferredFileName,
        int $iteration = 0
    ): string {
        $nextFileName = $this->getNextFileName($preferredFileName, $relatedMedia, $iteration);

        foreach ($relatedMedia as $media) {
            if ($media->hasFile() && $media->getFileName() === $nextFileName) {
                return $this->getPossibleFileName($relatedMedia, $preferredFileName, $iteration + 1);
            }
        }

        return $nextFileName;
    }
}
