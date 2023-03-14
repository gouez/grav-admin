<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\DataAbstractionLayer;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailEntity;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Content\Media\Pathname\UrlGeneratorInterface;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\QueueTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
class MediaThumbnailRepositoryTest extends TestCase
{
    use IntegrationTestBehaviour;
    use QueueTestBehaviour;

    /**
     * @dataProvider deleteThumbnailProvider
     */
    public function testDeleteThumbnail(bool $private): void
    {
        $service = $private ? 'laser.filesystem.private' : 'laser.filesystem.public';

        $mediaId = Uuid::randomHex();

        $media = $this->createThumbnailWithMedia($mediaId, $private);

        $thumbnailPath = $this->createThumbnailFile($media, $service);

        $thumbnailIds = $this->getContainer()->get('media_thumbnail.repository')
            ->searchIds(new Criteria(), Context::createDefaultContext());

        $delete = \array_values(\array_map(static fn ($id) => ['id' => $id], $thumbnailIds->getIds()));

        $this->getContainer()->get('media_thumbnail.repository')->delete($delete, Context::createDefaultContext());
        $this->runWorker();

        static::assertFalse($this->getFilesystem($service)->has($thumbnailPath));
    }

    public static function deleteThumbnailProvider(): \Generator
    {
        yield 'Test private filesystem' => [true];
        yield 'Test public filesystem' => [true];
    }

    private function createThumbnailWithMedia(string $mediaId, bool $private): MediaEntity
    {
        $this->getContainer()->get('media.repository')->create([
            [
                'id' => $mediaId,
                'name' => 'test media',
                'fileExtension' => 'png',
                'mimeType' => 'image/png',
                'fileName' => $mediaId . '-' . (new \DateTime())->getTimestamp(),
                'private' => $private,
                'thumbnails' => [
                    [
                        'width' => 100,
                        'height' => 200,
                        'highDpi' => false,
                    ],
                ],
            ],
        ], Context::createDefaultContext());

        return $this->getContainer()->get('media.repository')
            ->search(new Criteria([$mediaId]), Context::createDefaultContext())
            ->get($mediaId);
    }

    private function createThumbnailFile(MediaEntity $media, string $service): string
    {
        $generator = $this->getContainer()->get(UrlGeneratorInterface::class);

        $thumbnail = (new MediaThumbnailEntity())->assign(['width' => 100, 'height' => 200]);

        $thumbnailPath = $generator->getRelativeThumbnailUrl($media, $thumbnail);

        $this->getFilesystem($service)->write($thumbnailPath, 'foo');

        return $thumbnailPath;
    }
}
