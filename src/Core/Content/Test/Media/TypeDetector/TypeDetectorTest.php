<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\TypeDetector;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaType\AudioType;
use Laser\Core\Content\Media\MediaType\BinaryType;
use Laser\Core\Content\Media\MediaType\DocumentType;
use Laser\Core\Content\Media\MediaType\ImageType;
use Laser\Core\Content\Media\MediaType\VideoType;
use Laser\Core\Content\Media\TypeDetector\TypeDetector;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class TypeDetectorTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testDetectGif(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/logo.gif')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectAnimatedGif(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/animated.gif')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectWebp(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/laser-logo.vp8x.webp')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectAnimatedWebp(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/animated.webp')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(2, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
        static::assertTrue($type->is(ImageType::ANIMATED));
    }

    public function testDetectSvg(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/logo-version-professionalplus.svg')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::VECTOR_GRAPHIC));
    }

    public function testDetectJpg(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/laser.jpg')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(0, $type->getFlags());
    }

    public function testDetectPng(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/laser-logo.png')
        );

        static::assertInstanceOf(ImageType::class, $type);
        static::assertCount(1, $type->getFlags());
        static::assertTrue($type->is(ImageType::TRANSPARENT));
    }

    public function testDetectDoc(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/reader.doc')
        );

        static::assertInstanceOf(DocumentType::class, $type);
    }

    public function testDetectDocx(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/reader.docx')
        );

        static::assertInstanceOf(DocumentType::class, $type);
    }

    public function testDetectPdf(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.pdf')
        );

        static::assertInstanceOf(DocumentType::class, $type);
    }

    public function testDetectAvi(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.avi')
        );

        static::assertInstanceOf(VideoType::class, $type);
    }

    public function testDetectMov(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.mov')
        );

        static::assertInstanceOf(VideoType::class, $type);
    }

    public function testDetectMp4(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.mp4')
        );

        static::assertInstanceOf(VideoType::class, $type);
    }

    public function testDetectWebm(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/small.webm')
        );

        static::assertInstanceOf(VideoType::class, $type);
    }

    public function testDetectIso(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/test.iso')
        );

        static::assertInstanceOf(BinaryType::class, $type);
    }

    public function testDetectMp3(): void
    {
        $type = $this->getTypeDetector()->detect(
            $this->createMediaFile(__DIR__ . '/../fixtures/file_example.mp3')
        );

        static::assertInstanceOf(AudioType::class, $type);
    }

    private function getTypeDetector(): TypeDetector
    {
        return $this->getContainer()->get(TypeDetector::class);
    }

    private function createMediaFile(string $filePath): MediaFile
    {
        static::assertIsString($mimeContentType = mime_content_type($filePath));
        static::assertIsInt($filesize = filesize($filePath));

        return new MediaFile(
            $filePath,
            $mimeContentType,
            pathinfo($filePath, \PATHINFO_EXTENSION),
            $filesize
        );
    }
}
