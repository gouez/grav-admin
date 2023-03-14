<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\Cms;

use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\Cms\DefaultMediaResolver;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class DefaultMediaResolverTest extends TestCase
{
    use IntegrationTestBehaviour;

    private DefaultMediaResolver $mediaResolver;

    private FilesystemOperator $publicFilesystem;

    public function setUp(): void
    {
        $this->publicFilesystem = $this->getPublicFilesystem();
        $this->mediaResolver = new DefaultMediaResolver($this->publicFilesystem);
    }

    public function testGetDefaultMediaEntityWithoutValidFileName(): void
    {
        $media = $this->mediaResolver->getDefaultCmsMediaEntity('this/file/does/not/exists');

        static::assertNull($media);
    }

    public function testGetDefaultMediaEntityWithValidFileName(): void
    {
        $this->publicFilesystem->write('/bundles/core/assets/default/cms/laser.jpg', '');
        $media = $this->mediaResolver->getDefaultCmsMediaEntity('core/assets/default/cms/laser.jpg');

        static::assertInstanceOf(MediaEntity::class, $media);
        static::assertEquals('laser', $media->getFileName());
        static::assertEquals('image/jpeg', $media->getMimeType());
        static::assertEquals('jpg', $media->getFileExtension());
    }
}
