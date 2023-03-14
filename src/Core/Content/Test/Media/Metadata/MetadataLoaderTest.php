<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Media\Metadata;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Media\File\MediaFile;
use Laser\Core\Content\Media\MediaType\DocumentType;
use Laser\Core\Content\Media\MediaType\ImageType;
use Laser\Core\Content\Media\Metadata\MetadataLoader;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

/**
 * @internal
 */
class MetadataLoaderTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testJpg(): void
    {
        $result = $this
            ->getMetadataLoader()
            ->loadFromFile($this->createMediaFile(__DIR__ . '/../fixtures/laser.jpg'), new ImageType());

        $expected = [
            'type' => \IMAGETYPE_JPEG,
            'width' => 1530,
            'height' => 1021,
        ];

        static::assertIsArray($result);
        static::assertEquals($expected, $result);
    }

    public function testGif(): void
    {
        $result = $this
            ->getMetadataLoader()
            ->loadFromFile($this->createMediaFile(__DIR__ . '/../fixtures/logo.gif'), new ImageType());

        $expected = [
            'type' => \IMAGETYPE_GIF,
            'width' => 142,
            'height' => 37,
        ];

        static::assertIsArray($result);
        static::assertEquals($expected, $result);
    }

    public function testPng(): void
    {
        $result = $this
            ->getMetadataLoader()
            ->loadFromFile($this->createMediaFile(__DIR__ . '/../fixtures/laser-logo.png'), new ImageType());

        $expected = [
            'type' => \IMAGETYPE_PNG,
            'width' => 499,
            'height' => 266,
        ];

        static::assertIsArray($result);
        static::assertEquals($expected, $result);
    }

    public function testSvg(): void
    {
        $result = $this
            ->getMetadataLoader()
            ->loadFromFile($this->createMediaFile(__DIR__ . '/../fixtures/logo-version-professionalplus.svg'), new ImageType());

        static::assertNull($result);
    }

    public function testPdf(): void
    {
        $result = $this
            ->getMetadataLoader()
            ->loadFromFile($this->createMediaFile(__DIR__ . '/../fixtures/small.pdf'), new DocumentType());

        static::assertNull($result);
    }

    private function getMetadataLoader(): MetadataLoader
    {
        return $this->getContainer()
            ->get(MetadataLoader::class);
    }

    private function createMediaFile(string $filePath): MediaFile
    {
        return new MediaFile(
            $filePath,
            mime_content_type($filePath),
            pathinfo($filePath, \PATHINFO_EXTENSION),
            filesize($filePath)
        );
    }
}
