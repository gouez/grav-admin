<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\File;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\File\FileFetcher;
use Laser\Core\Content\Media\File\FileLoader;
use Laser\Core\Content\Media\File\FileSaver;
use Laser\Core\Content\Test\Media\MediaFixtures;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
final class FileLoaderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use MediaFixtures;

    public const TEST_IMAGE = __DIR__ . '/../fixtures/laser-logo.png';

    private FileLoader $fileLoader;

    private FileFetcher $fileFetcher;

    private FileSaver $fileSaver;

    private EntityRepository $mediaRepository;

    protected function setUp(): void
    {
        $this->fileLoader = $this->getContainer()->get(FileLoader::class);
        $this->fileFetcher = $this->getContainer()->get(FileFetcher::class);
        $this->fileSaver = $this->getContainer()->get(FileSaver::class);
        $this->mediaRepository = $this->getContainer()->get('media.repository');
    }

    public function testLoadMediaFile(): void
    {
        $context = Context::createDefaultContext();
        $blob = \file_get_contents(self::TEST_IMAGE);
        static::assertIsString($blob);
        $mediaFile = $this->fileFetcher->fetchBlob($blob, 'png', 'image/png');
        $mediaId = Uuid::randomHex();
        $this->mediaRepository->create([['id' => $mediaId]], $context);
        $this->fileSaver->persistFileToMedia($mediaFile, $mediaId . '.png', $mediaId, $context);

        static::assertSame($blob, $this->fileLoader->loadMediaFile($mediaId, $context));

        $this->mediaRepository->delete([['id' => $mediaId]], $context);
    }

    public function testLoadMediaFileStream(): void
    {
        $context = Context::createDefaultContext();
        $blob = \file_get_contents(self::TEST_IMAGE);
        static::assertIsString($blob);
        $mediaFile = $this->fileFetcher->fetchBlob($blob, 'png', 'image/png');
        $mediaId = Uuid::randomHex();
        $this->mediaRepository->create([['id' => $mediaId]], $context);
        $this->fileSaver->persistFileToMedia($mediaFile, $mediaId . '.png', $mediaId, $context);

        static::assertSame($blob, (string) $this->fileLoader->loadMediaFileStream($mediaId, $context));

        $this->mediaRepository->delete([['id' => $mediaId]], $context);
    }
}
